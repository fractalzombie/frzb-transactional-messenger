<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2024 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\TransactionalMessenger\ValueObject;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Helper\TransactionHelper;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;

#[Immutable]
final readonly class PendingEnvelope
{
    public function __construct(
        public Envelope $envelope,
        public \DateTimeImmutable $whenPended = new \DateTimeImmutable(),
    ) {}

    public function getMessageClass(): string
    {
        return $this->envelope->getMessage()::class;
    }

    public function isTransactional(CommitType ...$commitTypes): bool
    {
        return TransactionHelper::isDispatchable($this->getMessageClass(), ...$commitTypes);
    }
}
