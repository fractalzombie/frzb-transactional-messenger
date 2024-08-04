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

namespace FRZB\Component\TransactionalMessenger\Storage;

/**
 * @psalm-template TValue of object
 */
interface StorageInterface
{
    /**
     * @psalm-param \Iterator<TValue>|self<TValue> $storage
     *
     * @psalm-return static<TValue>
     */
    public function init(iterable|self $storage): static;

    /**
     * @psalm-param TValue ...$items
     *
     * @psalm-return static<TValue>
     */
    public function append(object ...$items): static;

    /**
     * @psalm-param TValue ...$items
     *
     * @psalm-return static<TValue>
     */
    public function prepend(object ...$items): static;

    /** @psalm-return ?TValue */
    public function next(): ?object;

    /** @psalm-return \Iterator<TValue> */
    public function iterate(): iterable;

    /**
     * @psalm-template RType of object
     *
     * @psalm-param callable<TValue> $callback
     *
     * @psalm-return static<RType>
     */
    public function map(callable $callback): static;

    /**
     * @psalm-param callable<TValue> $callback
     *
     * @psalm-return static<TValue>
     */
    public function filter(callable $callback): static;

    /**
     * @psalm-param \Iterator<TValue>|self<TValue> $storage
     *
     * @psalm-return static<TValue>
     */
    public function merge(iterable|self $storage): static;

    /** Clear envelope storage */
    public function clear(): static;

    /** @psalm-return \Iterator<TValue> */
    public function list(): iterable;

    /** Count of envelopes in storage */
    public function count(): int;
}
