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

namespace FRZB\Component\TransactionalMessenger\Event;

use FRZB\Component\TransactionalMessenger\ValueObject\SucceedEnvelope;
use JetBrains\PhpStorm\Immutable;
use Symfony\Contracts\EventDispatcher\Event;

#[Immutable]
final class DispatchSucceedEvent extends Event
{
    public function __construct(
        public readonly SucceedEnvelope $envelope,
    ) {}
}
