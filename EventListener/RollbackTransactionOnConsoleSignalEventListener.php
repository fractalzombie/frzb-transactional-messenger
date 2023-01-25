<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Exception\DispatchException;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface as RollbackService;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleSignalEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ConsoleEvents::SIGNAL, priority: Transactional::LISTENER_PRIORITY)]
class RollbackTransactionOnConsoleSignalEventListener
{
    public function __construct(
        private readonly RollbackService $service,
    ) {
    }

    public function __invoke(ConsoleSignalEvent $event): void
    {
        $this->service->rollback(DispatchException::fromSignal($event));
    }
}
