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

namespace FRZB\Component\TransactionalMessenger\Helper;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/** @internal */
#[Immutable]
final class EnvelopeHelper
{
    private function __construct() {}

    public static function wrap(object $message): Envelope
    {
        $envelope = Envelope::wrap($message);
        $hasDelayedStamp = null !== $envelope->last(DispatchAfterCurrentBusStamp::class);

        return $hasDelayedStamp ? $envelope : $envelope->with(new DispatchAfterCurrentBusStamp());
    }
}
