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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\MessageBus;

use FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent;
use FRZB\Component\TransactionalMessenger\Exception\MessageBusException;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBus;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBusInterface;
use FRZB\Component\TransactionalMessenger\Storage\Storage;
use FRZB\Component\TransactionalMessenger\Storage\StorageInterface;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/** @internal */
#[Group('transactional-messenger')]
final class TransactionalMessageBusRollbackTest extends TestCase
{
    private StorageInterface $pendingStorage;
    private StorageInterface $succeedStorage;
    private StorageInterface $failedStorage;

    private MessageBusInterface $decoratedBus;
    private EventDispatcherInterface $eventDispatcher;
    private TransactionalMessageBusInterface $messageBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingStorage = new Storage();
        $this->succeedStorage = new Storage();
        $this->failedStorage = new Storage();

        $this->decoratedBus = $this->createMock(MessageBusInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->messageBus = new TransactionalMessageBus($this->decoratedBus, $this->eventDispatcher, $this->pendingStorage, $this->succeedStorage, $this->failedStorage);
    }

    #[DataProvider('dataProvider')]
    public function testRollbackMethod(
        object $message,
        int $pendingCount,
        int $succeedCount,
        int $failedCount,
        int $expectsEventDispatcher,
        bool $isEventDispatcherThrows,
    ): void {
        $this->decoratedBus
            ->expects(self::never())
            ->method('dispatch')
            ->willReturn(EnvelopeHelper::wrap($message))
        ;

        if ($isEventDispatcherThrows) {
            $this->eventDispatcher
                ->expects(self::exactly($expectsEventDispatcher))
                ->method('dispatch')
                ->willThrowException(new \Exception('Something goes wrong'))
            ;

            $this->expectException(MessageBusException::class);
        } else {
            $this->eventDispatcher
                ->expects(self::exactly($expectsEventDispatcher))
                ->method('dispatch')
                ->willReturnCallback(fn (DispatchFailedEvent $event) => self::assertSame(spl_object_hash($message), spl_object_hash($event->envelope->envelope->getMessage())))
            ;
        }

        $envelope = $this->messageBus->dispatch($message);
        $this->messageBus->rollback(new \Exception('Something goes wrong'));

        self::assertSame(spl_object_hash($message), spl_object_hash($envelope->getMessage()));
        self::assertSame($pendingCount, $this->pendingStorage->count());
        self::assertSame($succeedCount, $this->succeedStorage->count());
        self::assertSame($failedCount, $this->failedStorage->count());
    }

    public static function dataProvider(): iterable
    {
        yield \sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'message' => new TransactionalOnTerminateMessage(),
            'pendingCount' => 0,
            'succeedCount' => 0,
            'failedCount' => 0,
            'expectsEventDispatcher' => 1,
            'isEventDispatcherThrows' => false,
        ];

        yield \sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'message' => new TransactionalOnResponseMessage(),
            'pendingCount' => 0,
            'succeedCount' => 0,
            'failedCount' => 0,
            'expectsEventDispatcher' => 1,
            'isEventDispatcherThrows' => false,
        ];

        yield \sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'message' => new TransactionalOnHandledMessage(),
            'pendingCount' => 0,
            'succeedCount' => 0,
            'failedCount' => 0,
            'expectsEventDispatcher' => 1,
            'isEventDispatcherThrows' => false,
        ];

        yield \sprintf('%s event dispatcher throws', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'message' => new TransactionalOnHandledMessage(),
            'pendingCount' => 0,
            'succeedCount' => 0,
            'failedCount' => 0,
            'expectsEventDispatcher' => 1,
            'isEventDispatcherThrows' => true,
        ];
    }
}
