<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Exception\MessageBusException;

#[AsAlias(TransactionalMessageBus::class)]
interface CommitTransactionInterface
{
    /** @throws MessageBusException */
    public function commit(CommitType ...$commitTypes): void;
}
