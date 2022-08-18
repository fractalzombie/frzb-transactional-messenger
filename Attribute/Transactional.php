<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Attribute;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Transactional
{
    public const LISTENER_PRIORITY = -2048;

    /** @var array<CommitType> */
    public readonly array $commitTypes;

    public function __construct(
        CommitType ...$commitTypes,
    ) {
        $this->commitTypes = $commitTypes ?: [CommitType::OnTerminate];
    }
}
