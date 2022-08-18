<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\ValueObject;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;

/** @template T of object */
#[Immutable]
final class SucceedEnvelope
{
    public function __construct(
        public readonly Envelope $envelope,
        public readonly \DateTimeImmutable $whenDispatched = new \DateTimeImmutable(),
    ) {
    }
}
