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
 * @psalm-template TValue
 *
 * @template-implements StorageInterface<TValue>
 */
class Storage implements StorageInterface
{
    public function __construct(
        /** @param \Iterator<TValue> $items */
        private iterable $items = [],
    ) {}

    public function init(iterable|StorageInterface $storage): static
    {
        $this->items = $storage instanceof StorageInterface ? $storage->list() : $storage;

        return $this;
    }

    public function append(object ...$items): static
    {
        array_push($this->items, ...$items);

        return $this;
    }

    public function prepend(object ...$items): static
    {
        array_unshift($this->items, ...$items);

        return $this;
    }

    public function next(): ?object
    {
        return array_shift($this->items);
    }

    public function iterate(): iterable
    {
        while ($item = $this->next()) {
            yield $item;
        }
    }

    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items));
    }

    public function filter(callable $callback): static
    {
        return new static(array_filter($this->items, $callback));
    }

    public function merge(iterable|StorageInterface $storage): static
    {
        $this->items = [...$this->items, ...($storage instanceof StorageInterface ? $storage->list() : $storage)];

        return $this;
    }

    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    public function list(): iterable
    {
        return $this->items;
    }

    public function count(): int
    {
        return \count($this->items);
    }
}
