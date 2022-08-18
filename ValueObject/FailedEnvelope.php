<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\ValueObject;

use FRZB\Component\TransactionalMessenger\Exception\DispatchException;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;

/** @template T of object */
#[Immutable]
final class FailedEnvelope
{
    public readonly DispatchException $exception;

    public function __construct(
        public readonly Envelope $envelope,
        \Throwable $exception,
        public readonly \DateTimeImmutable $whenFailed = new \DateTimeImmutable(),
    ) {
        $this->exception = DispatchException::fromThrowable($exception);
    }
}
