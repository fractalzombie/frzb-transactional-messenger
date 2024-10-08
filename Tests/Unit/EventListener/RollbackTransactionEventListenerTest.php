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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\EventListener;

use FRZB\Component\TransactionalMessenger\EventListener\RollbackTransactionOnExceptionEventListener;
use FRZB\Component\TransactionalMessenger\EventListener\RollbackTransactionOnMessageFailedEventListener;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Kernel;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

/** @internal */
#[Group('transactional-messenger')]
final class RollbackTransactionEventListenerTest extends TestCase
{
    private RollbackTransactionInterface $rollbackService;

    protected function setUp(): void
    {
        $this->rollbackService = $this->createMock(RollbackTransactionInterface::class);
    }

    #[DataProvider('dataProvider')]
    public function testInvokeMethod(string $eventListenerClass, object $event): void
    {
        $this->rollbackService
            ->expects(self::once())
            ->method('rollback')
        ;

        (new $eventListenerClass($this->rollbackService))($event);
    }

    public static function dataProvider(): iterable
    {
        yield ClassHelper::getShortName(RollbackTransactionOnExceptionEventListener::class) => [
            'eventListenerClass' => RollbackTransactionOnExceptionEventListener::class,
            'event' => new ExceptionEvent(new Kernel('test', false), Request::createFromGlobals(), HttpKernelInterface::MAIN_REQUEST, new \Exception('Something goes wrong')),
        ];

        yield ClassHelper::getShortName(RollbackTransactionOnMessageFailedEventListener::class) => [
            'eventListenerClass' => RollbackTransactionOnMessageFailedEventListener::class,
            'event' => new WorkerMessageFailedEvent(EnvelopeHelper::wrap(new TransactionalOnTerminateMessage()), 'default.receiver', new \Exception('Something goes wrong')),
        ];
    }
}
