<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Storage;

/**
 * @template T of object
 */
interface StorageInterface
{
    /**
     * @param \Iterator<T>|self<T> $storage
     *
     * @return static<T>
     */
    public function init(iterable|self $storage): static;

    /**
     * @param T ...$items
     *
     * @return static<T>
     */
    public function append(object ...$items): static;

    /**
     * @param T ...$items
     *
     * @return static<T>
     */
    public function prepend(object ...$items): static;

    /** @return null|T */
    public function next(): ?object;

    /** @return \Iterator<T> */
    public function iterate(): iterable;

    /**
     * @template R of object
     *
     * @param callable<T> $callback
     *
     * @return static<R>
     */
    public function map(callable $callback): static;

    /**
     * @param callable<T> $callback
     *
     * @return static<T>
     */
    public function filter(callable $callback): static;

    /**
     * @param \Iterator<T>|self<T> $storage
     *
     * @return static<T>
     */
    public function merge(iterable|self $storage): static;

    /** Clear envelope storage */
    public function clear(): static;

    /** @return \Iterator<T> */
    public function list(): iterable;

    /** Count of envelopes in storage */
    public function count(): int;
}
