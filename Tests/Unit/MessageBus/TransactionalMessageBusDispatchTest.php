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

use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBus as TransactionalMessageBusImpl;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBusInterface as TransactionalMessageBus;
use FRZB\Component\TransactionalMessenger\Storage\Storage as StorageImpl;
use FRZB\Component\TransactionalMessenger\Storage\StorageInterface as Storage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\NonTransactionalMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface as MessageBus;

/** @internal */
#[Group('transactional-messenger')]
final class TransactionalMessageBusDispatchTest extends TestCase
{
    private Storage $pendingStorage;

    private MessageBus $decoratedBus;
    private EventDispatcher $eventDispatcher;
    private TransactionalMessageBus $messageBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingStorage = new StorageImpl();

        $this->decoratedBus = $this->createMock(MessageBus::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->messageBus = new TransactionalMessageBusImpl($this->decoratedBus, $this->eventDispatcher, $this->pendingStorage);
    }

    #[DataProvider('dataProvider')]
    public function testDispatchMethod(object $message, int $pendingCount, int $expectsDecoratedDispatcher, int $expectsEventDispatcher): void
    {
        $this->decoratedBus
            ->expects(self::exactly($expectsDecoratedDispatcher))
            ->method('dispatch')
            ->willReturn(EnvelopeHelper::wrap($message))
        ;

        $this->eventDispatcher
            ->expects(self::exactly($expectsEventDispatcher))
            ->method('dispatch')
        ;

        $envelope = $this->messageBus->dispatch($message);

        self::assertSame(spl_object_hash($message), spl_object_hash($envelope->getMessage()));
        self::assertSame($pendingCount, $this->pendingStorage->count());
    }

    public static function dataProvider(): iterable
    {
        yield \sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'message' => new TransactionalOnTerminateMessage(),
            'pendingCount' => 1,
            'expectsDecoratedDispatcher' => 0,
            'expectsEventDispatcher' => 0,
        ];

        yield \sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'message' => new TransactionalOnResponseMessage(),
            'pendingCount' => 1,
            'expectsDecoratedDispatcher' => 0,
            'expectsEventDispatcher' => 0,
        ];

        yield \sprintf('%s is dispatched immediately', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'message' => new NonTransactionalMessage(),
            'pendingCount' => 0,
            'expectsDecoratedDispatcher' => 1,
            'expectsEventDispatcher' => 0,
        ];
    }
}
