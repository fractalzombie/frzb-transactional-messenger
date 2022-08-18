<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(TransactionalMessageBus::class)]
interface TransactionalMessageBusInterface extends DispatchTransactionInterface, CommitTransactionInterface, RollbackTransactionInterface
{
}
