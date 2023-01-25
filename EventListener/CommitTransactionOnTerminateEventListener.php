<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\EventListener;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\MessageBus\CommitTransactionInterface as CommitService;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(ConsoleEvents::TERMINATE, priority: Transactional::LISTENER_PRIORITY)]
#[AsEventListener(KernelEvents::TERMINATE, priority: Transactional::LISTENER_PRIORITY)]
class CommitTransactionOnTerminateEventListener
{
    public function __construct(
        private readonly CommitService $service,
    ) {
    }

    public function __invoke(): void
    {
        $this->service->commit(CommitType::OnTerminate);
    }
}
