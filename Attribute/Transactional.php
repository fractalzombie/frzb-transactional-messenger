<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Attribute;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Transactional
{
    public const LISTENER_PRIORITY = -2048;

    public function __construct(
        public readonly CommitType $commitType = CommitType::OnTerminate,
    ) {
    }
}
