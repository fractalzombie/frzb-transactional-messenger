<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\ValueObject;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;

/** @template T of object */
#[Immutable]
final class PendingEnvelope
{
    public function __construct(
        public readonly Envelope $envelope,
        public readonly \DateTimeImmutable $whenPended = new \DateTimeImmutable(),
    ) {
    }

    public function getMessageClass(): string
    {
        return $this->envelope->getMessage()::class;
    }
}
