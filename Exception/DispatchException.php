<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2024 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

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
