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

/** @internal */
#[Immutable]
final class ClassHelper
{
    final public const DEFAULT_SHORT_NAME = 'InvalidClassName';

    private function __construct() {}

    public static function getShortName(object|string $target): string
    {
        return self::getReflectionClass($target)?->getShortName() ?? self::DEFAULT_SHORT_NAME;
    }

    public static function getInheritanceList(object|string $target): array
    {
        $isTargetObject = is_object($target);
        $targetClass = $isTargetObject ? $target::class : $target;

        if (!class_exists($targetClass)) {
            return [];
        }

        return [$targetClass, ...(class_parents($target, false) ?: [])];
    }

    /**
     * @template T
     *
     * @param class-string<T>|T $target
     *
     * @return null|\ReflectionClass<T>
     */
    public static function getReflectionClass(object|string $target): ?\ReflectionClass
    {
        try {
            return $target instanceof \ReflectionClass ? $target : new \ReflectionClass($target);
        } catch (\ReflectionException) {
            return null;
        }
    }

    /**
     * @template T
     *
     * @param class-string<T>|T $target
     *
     * @return null|\ReflectionClass<T>
     */
    public static function getParentReflectionClass(object|string $target): ?\ReflectionClass
    {
        return self::getReflectionClass($target)?->getParentClass() ?: null;
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return \Iterator<\ReflectionAttribute<T>>
     */
    public static function getReflectionAttributes(object|string $target, string $attributeClass): iterable
    {
        return self::getReflectionClass($target)?->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) ?? [];
    }
}
