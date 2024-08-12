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

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Exception\DispatchException;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleSignalEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ConsoleEvents::SIGNAL, priority: Transactional::LISTENER_PRIORITY)]
class RollbackTransactionOnConsoleSignalEventListener
{
    public function __construct(
        private readonly RollbackTransactionInterface $service,
    ) {}

    public function __invoke(ConsoleSignalEvent $event): void
    {
        $this->service->rollback(DispatchException::fromSignal($event));
    }
}
