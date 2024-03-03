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

use FRZB\Component\TransactionalMessenger\Exception\DispatchException;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;

/** @template T of object */
#[Immutable]
final class FailedEnvelope
{
    public readonly DispatchException $exception;

    public function __construct(
        public readonly Envelope $envelope,
        \Throwable $exception,
        public readonly \DateTimeImmutable $whenFailed = new \DateTimeImmutable(),
    ) {
        $this->exception = $exception instanceof DispatchException ? $exception : DispatchException::fromThrowable($exception);
    }
}
