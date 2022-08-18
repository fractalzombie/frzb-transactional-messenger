<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\MessageBus;

use FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBus as TransactionalMessageBusImpl;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBusInterface as TransactionalMessageBus;
use FRZB\Component\TransactionalMessenger\Storage\Storage as StorageImpl;
use FRZB\Component\TransactionalMessenger\Storage\StorageInterface as Storage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface as MessageBus;

/** @internal */
#[Group('transactional-messenger')]
final class TransactionalMessageBusRollbackTest extends TestCase
{
    private Storage $pendingStorage;
    private Storage $succeedStorage;
    private Storage $failedStorage;

    private MessageBus $decoratedBus;
    private EventDispatcher $eventDispatcher;
    private TransactionalMessageBus $messageBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingStorage = new StorageImpl();
        $this->succeedStorage = new StorageImpl();
        $this->failedStorage = new StorageImpl();

        $this->decoratedBus = $this->createMock(MessageBus::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->messageBus = new TransactionalMessageBusImpl($this->decoratedBus, $this->eventDispatcher, $this->pendingStorage, $this->succeedStorage, $this->failedStorage);
    }

    #[DataProvider('dataProvider')]
    public function testRollbackMethod(
        object $message,
        int $pendingCount,
        int $succeedCount,
        int $failedCount,
        int $expectsEventDispatcher
    ): void {
        $this->decoratedBus
            ->expects(self::never())
            ->method('dispatch')
            ->willReturn(EnvelopeHelper::wrap($message))
        ;

        $this->eventDispatcher
            ->expects(self::exactly($expectsEventDispatcher))
            ->method('dispatch')
            ->willReturnCallback(fn (DispatchFailedEvent $event) => self::assertSame(spl_object_hash($message), spl_object_hash($event->envelope->envelope->getMessage())))
        ;

        $envelope = $this->messageBus->dispatch($message);
        $this->messageBus->rollback(new \Exception('Something goes wrong'));

        self::assertSame(spl_object_hash($message), spl_object_hash($envelope->getMessage()));
        self::assertSame($pendingCount, $this->pendingStorage->count());
        self::assertSame($succeedCount, $this->succeedStorage->count());
        self::assertSame($failedCount, $this->failedStorage->count());
    }

    public function dataProvider(): iterable
    {
        yield sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'message' => new TransactionalOnTerminateMessage(),
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_event_dispatcher' => 1,
        ];

        yield sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'message' => new TransactionalOnResponseMessage(),
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_event_dispatcher' => 1,
        ];

        yield sprintf('%s is dispatched delayed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'message' => new TransactionalOnHandledMessage(),
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_event_dispatcher' => 1,
        ];
    }
}
