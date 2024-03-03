<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2024 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsDecorator;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent;
use FRZB\Component\TransactionalMessenger\Event\DispatchSucceedEvent;
use FRZB\Component\TransactionalMessenger\Exception\MessageBusException;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\Helper\TransactionHelper;
use FRZB\Component\TransactionalMessenger\Storage\Storage as StorageImpl;
use FRZB\Component\TransactionalMessenger\Storage\StorageInterface as Storage;
use FRZB\Component\TransactionalMessenger\ValueObject\FailedEnvelope;
use FRZB\Component\TransactionalMessenger\ValueObject\PendingEnvelope;
use FRZB\Component\TransactionalMessenger\ValueObject\SucceedEnvelope;
use Psr\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface as MessageBus;
use Symfony\Contracts\EventDispatcher\Event;

#[AsService]
#[AsDecorator(MessageBus::class)]
final class TransactionalMessageBus implements TransactionalMessageBusInterface
{
    /** @var Storage<PendingEnvelope> */
    private readonly Storage $pendingStorage;

    /** @var Storage<SucceedEnvelope> */
    private readonly Storage $succeedStorage;

    /** @var Storage<FailedEnvelope> */
    private readonly Storage $failedStorage;

    public function __construct(
        private readonly MessageBus $decoratedBus,
        private readonly EventDispatcher $eventDispatcher,
        ?Storage $pendingStorage = null,
        ?Storage $succeedStorage = null,
        ?Storage $failedStorage = null,
    ) {
        $this->pendingStorage = $pendingStorage ?? new StorageImpl();
        $this->succeedStorage = $succeedStorage ?? new StorageImpl();
        $this->failedStorage = $failedStorage ?? new StorageImpl();
    }

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $envelope = EnvelopeHelper::wrap($message);

        TransactionHelper::isTransactional($message)
            ? $this->pendingStorage->append(new PendingEnvelope($envelope))
            : $this->dispatchEnvelope($envelope);

        return $envelope;
    }

    public function commit(CommitType ...$commitTypes): void
    {
        try {
            $this->dispatchPendingEnvelopes(...$commitTypes);
            $this->dispatchSucceedEnvelopes();
            $this->dispatchFailedEnvelopes();
        } catch (\Throwable $e) {
            throw MessageBusException::fromThrowable($e);
        }
    }

    public function rollback(\Throwable $exception): void
    {
        ArrayList::collect($this->pendingStorage->iterate())
            ->map(static fn (PendingEnvelope $pe) => new FailedEnvelope($pe->envelope, $exception))
            ->tap(fn (FailedEnvelope $fe) => $this->failedStorage->append($fe))
        ;

        try {
            $this->dispatchFailedEnvelopes();
        } catch (\Throwable $e) {
            throw MessageBusException::fromThrowable($e);
        } finally {
            $this->pendingStorage->clear();
            $this->succeedStorage->clear();
            $this->failedStorage->clear();
        }
    }

    private function dispatchPendingEnvelopes(CommitType ...$commitTypes): void
    {
        ArrayList::collect($this->pendingStorage->iterate())->tap(
            fn (PendingEnvelope $pe) => $pe->isTransactional(...$commitTypes)
                ? $this->dispatchEnvelope($pe->envelope)
                : $this->pendingStorage->prepend($pe),
        );
    }

    private function dispatchSucceedEnvelopes(): void
    {
        ArrayList::collect($this->succeedStorage->iterate())
            ->tap(fn (SucceedEnvelope $se) => $this->dispatchEvent(new DispatchSucceedEvent($se)))
        ;
    }

    private function dispatchFailedEnvelopes(): void
    {
        ArrayList::collect($this->failedStorage->iterate())
            ->tap(fn (FailedEnvelope $fe) => $this->dispatchEvent(new DispatchFailedEvent($fe)))
        ;
    }

    private function dispatchEnvelope(Envelope $envelope): Envelope
    {
        try {
            $this->succeedStorage->append(new SucceedEnvelope($this->decoratedBus->dispatch($envelope)));
        } catch (\Throwable $e) {
            $this->failedStorage->append(new FailedEnvelope($envelope, $e));
        } finally {
            return $envelope;
        }
    }

    private function dispatchEvent(Event $event): Event
    {
        $this->eventDispatcher->dispatch($event);

        return $event;
    }
}
