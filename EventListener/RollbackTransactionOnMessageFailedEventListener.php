<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\MessageBus\RollbackTransactionInterface as RollbackService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

#[AsEventListener(WorkerMessageFailedEvent::class, 'onMessageFailed', Transactional::LISTENER_PRIORITY)]
class RollbackTransactionOnMessageFailedEventListener
{
    public function __construct(
        private readonly RollbackService $service,
    ) {
    }

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $this->service->rollback($event->getThrowable());
    }
}
