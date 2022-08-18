<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface as RollbackService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION, priority: Transactional::LISTENER_PRIORITY)]
class RollbackTransactionOnExceptionEventListener
{
    public function __construct(
        private readonly RollbackService $service,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $this->service->rollback($event->getThrowable());
    }
}
