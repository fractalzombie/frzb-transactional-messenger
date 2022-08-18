<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use FRZB\Component\TransactionalMessenger\Exception\MessageBusException;

#[AsAlias(TransactionalMessageBus::class)]
interface RollbackTransactionInterface
{
    /** @throws MessageBusException */
    public function rollback(\Throwable $exception): void;
}
