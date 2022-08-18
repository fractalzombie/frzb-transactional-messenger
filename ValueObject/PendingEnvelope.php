<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\ValueObject;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Helper\AttributeHelper;
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

    public function getAttribute(): Transactional
    {
        return AttributeHelper::getAttribute($this->envelope->getMessage(), Transactional::class);
    }
}
