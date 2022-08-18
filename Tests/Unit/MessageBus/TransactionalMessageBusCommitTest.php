<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\MessageBus;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent;
use FRZB\Component\TransactionalMessenger\Event\DispatchSucceedEvent;
use FRZB\Component\TransactionalMessenger\Helper\AttributeHelper;
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
final class TransactionalMessageBusCommitTest extends TestCase
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
    public function testCommitMethod(
        object $message,
        string $eventClass,
        int $pendingCount,
        int $succeedCount,
        int $failedCount,
        int $expectsDecoratedDispatcher,
        int $expectsEventDispatcher,
        bool $isThrows,
    ): void {
        if ($isThrows) {
            $this->decoratedBus
                ->expects(self::exactly($expectsDecoratedDispatcher))
                ->method('dispatch')
                ->willThrowException(new \Exception('Something goes wrong'))
            ;
        } else {
            $this->decoratedBus
                ->expects(self::exactly($expectsDecoratedDispatcher))
                ->method('dispatch')
                ->willReturn(EnvelopeHelper::wrap($message))
            ;
        }

        $this->eventDispatcher
            ->expects(self::exactly($expectsEventDispatcher))
            ->method('dispatch')
            ->willReturnCallback(fn (object $event) => self::assertInstanceOf($eventClass, $event))
        ;

        $envelope = $this->messageBus->dispatch($message);
        $this->messageBus->commit(AttributeHelper::getAttribute($message, Transactional::class)->commitType);

        self::assertSame(spl_object_hash($message), spl_object_hash($envelope->getMessage()));
        self::assertSame($pendingCount, $this->pendingStorage->count());
        self::assertSame($succeedCount, $this->succeedStorage->count());
        self::assertSame($failedCount, $this->failedStorage->count());
    }

    public function dataProvider(): iterable
    {
        yield sprintf('%s is succeed commit', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'message' => new TransactionalOnTerminateMessage(),
            'event_class' => DispatchSucceedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => false,
        ];

        yield sprintf('%s is succeed commit', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'message' => new TransactionalOnResponseMessage(),
            'event_class' => DispatchSucceedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => false,
        ];

        yield sprintf('%s is succeed commit', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'message' => new TransactionalOnHandledMessage(),
            'event_class' => DispatchSucceedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => false,
        ];

        yield sprintf('%s is failure commit', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'message' => new TransactionalOnTerminateMessage(),
            'event_class' => DispatchFailedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => true,
        ];

        yield sprintf('%s is failure commit', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'message' => new TransactionalOnResponseMessage(),
            'event_class' => DispatchFailedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => true,
        ];

        yield sprintf('%s is failure commit', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'message' => new TransactionalOnHandledMessage(),
            'event_class' => DispatchFailedEvent::class,
            'pending_count' => 0,
            'succeed_count' => 0,
            'failed_count' => 0,
            'expects_decorated_dispatcher' => 1,
            'expects_event_dispatcher' => 1,
            'is_throws' => true,
        ];
    }
}
