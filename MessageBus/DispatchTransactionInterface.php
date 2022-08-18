<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\MessageBus;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsAlias(TransactionalMessageBus::class)]
interface DispatchTransactionInterface extends MessageBusInterface
{
}
