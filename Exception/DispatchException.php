<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Exception;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Console\Event\ConsoleSignalEvent;

#[Immutable]
final class DispatchException extends \LogicException
{
    private const MESSAGE_SIGNAL_CONSOLE_EVENT = 'Rollback transaction: Message was interrupted in "%s" command on signal';

    public static function fromSignal(ConsoleSignalEvent $event): self
    {
        $message = sprintf(self::MESSAGE_SIGNAL_CONSOLE_EVENT, $event->getCommand());

        return new self($message, $event->getHandlingSignal());
    }

    public static function fromThrowable(\Throwable $previous): self
    {
        return new self($previous->getMessage(), (int) $previous->getCode(), $previous);
    }
}
