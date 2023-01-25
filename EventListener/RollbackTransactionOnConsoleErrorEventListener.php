<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface as RollbackService;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ConsoleEvents::ERROR, priority: Transactional::LISTENER_PRIORITY)]
class RollbackTransactionOnConsoleErrorEventListener
{
    public function __construct(
        private readonly RollbackService $service,
    ) {
    }

    public function __invoke(ConsoleErrorEvent $event): void
    {
        $this->service->rollback($event->getError());
    }
}
