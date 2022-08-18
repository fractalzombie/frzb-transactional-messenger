<?php

declare(strict_types=1);


namespace FRZB\Component\TransactionalMessenger\Helper;

use Fp\Collections\ArrayList;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\ValueObject\PendingEnvelope;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class TransactionHelper
{
    private function __construct()
    {
    }

    public static function isDispatchAllowed(PendingEnvelope $envelope, CommitType ...$commitTypes): bool
    {
        return ArrayList::collect($envelope->getAttribute()->commitTypes)
            ->filter(static fn (CommitType $ct) => \in_array($ct, $commitTypes, true))
            ->isNonEmpty()
        ;
    }
}
