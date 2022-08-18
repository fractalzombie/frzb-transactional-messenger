<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use FRZB\Component\DependencyInjection\Attribute\AsDecorator;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent;
use FRZB\Component\TransactionalMessenger\Event\DispatchSucceedEvent;
use FRZB\Component\TransactionalMessenger\Helper\AttributeHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
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

    /** {@inheritdoc} */
    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $envelope = EnvelopeHelper::wrap($message);
        $isTransactional = AttributeHelper::hasAttribute($envelope->getMessage(), Transactional::class);

        $isTransactional ? $this->pendingStorage->append(new PendingEnvelope($envelope)) : $this->dispatchEnvelope($envelope);

        return $envelope;
    }

    /** {@inheritdoc} */
    public function commit(CommitType ...$commitTypes): void
    {
        $this->dispatchPendingEnvelopes(...$commitTypes);
        $this->dispatchSucceedEnvelopes();
        $this->dispatchFailedEnvelopes();
    }

    /** {@inheritdoc} */
    public function rollback(\Throwable $exception): void
    {
        $this->failedStorage->merge(
            $this->pendingStorage->map(
                static fn (PendingEnvelope $pe) => new FailedEnvelope($pe->envelope, $exception),
            ),
        );

        $this->dispatchFailedEnvelopes();

        $this->pendingStorage->clear();
        $this->succeedStorage->clear();
        $this->failedStorage->clear();
    }

    private function dispatchPendingEnvelopes(CommitType ...$commitTypes): void
    {
        $notExecutedEnvelopes = new StorageImpl();

        while ($pendingEnvelope = $this->pendingStorage->next()) {
            \in_array($pendingEnvelope->getAttribute()->commitType, $commitTypes)
                ? $this->dispatchEnvelope($pendingEnvelope->envelope)
                : $notExecutedEnvelopes->prepend($pendingEnvelope)
            ;
        }

        $this->pendingStorage->prepend(...$notExecutedEnvelopes->list());
    }

    private function dispatchSucceedEnvelopes(): void
    {
        while ($succeedEnvelope = $this->succeedStorage->next()) {
            $this->dispatchEvent(new DispatchSucceedEvent($succeedEnvelope));
        }
    }

    private function dispatchFailedEnvelopes(): void
    {
        while ($failedEnvelope = $this->failedStorage->next()) {
            $this->dispatchEvent(new DispatchFailedEvent($failedEnvelope));
        }
    }

    private function dispatchEnvelope(Envelope $envelope): void
    {
        try {
            $this->succeedStorage->append(new SucceedEnvelope($this->decoratedBus->dispatch($envelope)));
        } catch (\Throwable $e) {
            $this->failedStorage->append(new FailedEnvelope($envelope, $e));
        }
    }

    private function dispatchEvent(Event $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
