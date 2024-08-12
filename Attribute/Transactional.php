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

namespace FRZB\Component\TransactionalMessenger\Attribute;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Transactional
{
    public const LISTENER_PRIORITY = -2048;

    /** @var CommitType[] */
    public readonly array $commitTypes;

    public function __construct(
        CommitType ...$commitTypes,
    ) {
        $this->commitTypes = $commitTypes ?: [CommitType::OnTerminate];
    }
}
