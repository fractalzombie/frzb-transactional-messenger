<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Event;

use FRZB\Component\TransactionalMessenger\ValueObject\FailedEnvelope;
use JetBrains\PhpStorm\Immutable;
use Symfony\Contracts\EventDispatcher\Event;

#[Immutable]
final class DispatchFailedEvent extends Event
{
    public function __construct(
        public readonly FailedEnvelope $envelope,
    ) {
    }
}
