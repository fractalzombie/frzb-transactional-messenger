<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class ClassHelper
{
    final public const DEFAULT_SHORT_NAME = 'InvalidClassName';

    private function __construct()
    {
    }

    public static function getShortName(string|object $target): string
    {
        return self::getReflectionClass($target)?->getShortName() ?? self::DEFAULT_SHORT_NAME;
    }

    /**
     * @template T
     *
     * @param class-string<T>|T $target
     *
     * @return null|\ReflectionClass<T>
     */
    public static function getReflectionClass(string|object $target): ?\ReflectionClass
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
    public static function getParentReflectionClass(string|object $target): ?\ReflectionClass
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
    public static function getReflectionAttributes(string|object $target, string $attributeClass): iterable
    {
        return self::getReflectionClass($target)?->getAttributes($attributeClass, \ReflectionAttribute::IS_INSTANCEOF) ?? [];
    }
}
