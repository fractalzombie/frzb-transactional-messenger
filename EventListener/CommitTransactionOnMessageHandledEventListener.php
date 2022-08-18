<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\MessageBus\CommitTransactionInterface as CommitService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

#[AsEventListener(WorkerMessageHandledEvent::class, priority: Transactional::LISTENER_PRIORITY)]
class CommitTransactionOnMessageHandledEventListener
{
    public function __construct(
        private readonly CommitService $service,
    ) {
    }

    public function __invoke(): void
    {
        $this->service->commit(...CommitType::cases());
    }
}
