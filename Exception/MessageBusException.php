<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Exception;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class MessageBusException extends \RuntimeException
{
    public static function fromThrowable(\Throwable $previous): self
    {
        return new self($previous->getMessage(), (int) $previous->getCode(), $previous);
    }
}
