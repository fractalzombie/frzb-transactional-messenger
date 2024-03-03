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

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class AttributeHelper
{
    private function __construct() {}

    public static function hasAttribute(object|string $target, string $attributeClass): bool
    {
        return !empty(self::getReflectionAttributes($target, $attributeClass));
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return null|T
     */
    public static function getAttribute(object|string $target, string $attributeClass): ?object
    {
        return ArrayList::collect(self::getAttributes($target, $attributeClass))
            ->firstElement()
            ->get()
        ;
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return array<T>
     */
    public static function getAttributes(object|string $target, string $attributeClass): array
    {
        return ArrayList::collect(self::getReflectionAttributes($target, $attributeClass))
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toList()
        ;
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return array<\ReflectionAttribute<T>>
     */
    public static function getReflectionAttributes(object|string $target, string $attributeClass): array
    {
        return Option::fromNullable(ClassHelper::getReflectionClass($target))
            ->map(
                static fn (\ReflectionClass $rClass) => Option::fromNullable(ClassHelper::getParentReflectionClass($rClass))
                    ->map(static fn (\ReflectionClass $rClass) => [...ClassHelper::getReflectionAttributes($rClass, $attributeClass), ...self::getReflectionAttributes($rClass, $attributeClass)])
                    ->getOrElse(ClassHelper::getReflectionAttributes($rClass, $attributeClass))
            )
            ->getOrElse([])
        ;
    }
}
