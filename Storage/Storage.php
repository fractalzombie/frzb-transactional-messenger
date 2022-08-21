<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Storage;

/**
 * @template T
 *
 * @extends StorageInterface<T>
 */
class Storage implements StorageInterface
{
    public function __construct(
        /** @param \Iterator<T> $items */
        private iterable $items = [],
    ) {
    }

    /** {@inheritdoc} */
    public function init(iterable|StorageInterface $storage): static
    {
        $this->items = $storage instanceof StorageInterface ? $storage->list() : $storage;

        return $this;
    }

    /** {@inheritdoc} */
    public function append(object ...$items): static
    {
        array_push($this->items, ...$items);

        return $this;
    }

    /** {@inheritdoc} */
    public function prepend(object ...$items): static
    {
        array_unshift($this->items, ...$items);

        return $this;
    }

    /** {@inheritdoc} */
    public function next(): ?object
    {
        return array_shift($this->items);
    }

    /** {@inheritdoc} */
    public function iterate(): iterable
    {
        while ($item = $this->next()) {
            yield $item;
        }
    }

    /** {@inheritdoc} */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items));
    }

    /** {@inheritdoc} */
    public function filter(callable $callback): static
    {
        return new static(array_filter($this->items, $callback));
    }

    /** {@inheritdoc} */
    public function merge(iterable|StorageInterface $storage): static
    {
        $this->items = [...$this->items, ...$storage instanceof StorageInterface ? $storage->list() : $storage];

        return $this;
    }

    /** {@inheritdoc} */
    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    /** {@inheritdoc} */
    public function list(): iterable
    {
        return $this->items;
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return \count($this->items);
    }
}
