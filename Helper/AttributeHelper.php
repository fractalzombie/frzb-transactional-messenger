<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class AttributeHelper
{
    private function __construct()
    {
    }

    public static function hasAttribute(string|object $target, string $attributeClass): bool
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
    public static function getAttribute(string|object $target, string $attributeClass): ?object
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
    public static function getAttributes(string|object $target, string $attributeClass): array
    {
        return ArrayList::collect(self::getReflectionAttributes($target, $attributeClass))
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toArray()
        ;
    }

    /**
     * @template T
     *
     * @param class-string<T> $attributeClass
     *
     * @return array<\ReflectionAttribute<T>>
     */
    public static function getReflectionAttributes(string|object $target, string $attributeClass): array
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
