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

    public static function isTransactional(string|object $target): bool
    {
        return AttributeHelper::hasAttribute($target, Transactional::class);
    }

    public static function getTransactional(string|object $target): array
    {
        return AttributeHelper::getAttributes($target, Transactional::class);
    }

    public static function isDispatchAllowed(string|object $target, CommitType ...$commitTypes): bool
    {
        return ArrayList::collect(self::getTransactional($target))
            ->map(static fn (Transactional $t) => $t->commitTypes)
            ->reduce(array_merge(...))
            ->toArrayList(static fn (array $cts) => ArrayList::collect($cts))
            ->every(static fn (CommitType $ct) => \in_array($ct, $commitTypes, true))
        ;
    }
}
