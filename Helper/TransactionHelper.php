<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use Fp\Collections\ArrayList;
use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
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

    public static function isTransactional(object $message): bool
    {
        return AttributeHelper::hasAttribute($message, Transactional::class);
    }

    public static function isDispatchAllowed(PendingEnvelope $envelope, CommitType ...$commitTypes): bool
    {
        $attributes = AttributeHelper::getAttributes($envelope->getMessageClass(), Transactional::class);
        $allowedCommitTypes = ArrayList::collect($attributes)
            ->map(static fn (Transactional $t) => $t->commitTypes)
            ->reduce(array_merge(...))
            ->getOrElse([])
        ;

        return ArrayList::collect($allowedCommitTypes)
            ->filter(static fn (CommitType $ct) => \in_array($ct, $commitTypes, true))
            ->isNonEmpty()
        ;
    }
}
