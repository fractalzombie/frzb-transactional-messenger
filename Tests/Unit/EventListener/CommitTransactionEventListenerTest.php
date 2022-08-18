<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\EventListener;

use FRZB\Component\TransactionalMessenger\EventListener\CommitTransactionOnMessageHandledEventListener;
use FRZB\Component\TransactionalMessenger\EventListener\CommitTransactionOnResponseEventListener;
use FRZB\Component\TransactionalMessenger\EventListener\CommitTransactionOnTerminateEventListener;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\MessageBus\CommitTransactionInterface as CommitService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('transactional-messenger')]
final class CommitTransactionEventListenerTest extends TestCase
{
    private CommitService $commitService;

    protected function setUp(): void
    {
        $this->commitService = $this->createMock(CommitService::class);
    }

    #[DataProvider('dataProvider')]
    public function testInvokeMethod(string $eventListenerClass): void
    {
        $this->commitService
            ->expects(self::once())
            ->method('commit')
        ;

        (new $eventListenerClass($this->commitService))();
    }

    public function dataProvider(): iterable
    {
        yield ClassHelper::getShortName(CommitTransactionOnTerminateEventListener::class) => [
            'event_listener' => CommitTransactionOnTerminateEventListener::class,
        ];

        yield ClassHelper::getShortName(CommitTransactionOnResponseEventListener::class) => [
            'event_listener' => CommitTransactionOnResponseEventListener::class,
        ];

        yield ClassHelper::getShortName(CommitTransactionOnMessageHandledEventListener::class) => [
            'event_listener' => CommitTransactionOnMessageHandledEventListener::class,
        ];
    }
}
