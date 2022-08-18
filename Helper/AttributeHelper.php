<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use Fp\Collections\ArrayList;
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
        return null !== self::getAttribute($target, $attributeClass);
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
        try {
            $attributes = (new \ReflectionClass($target))->getAttributes($attributeClass);
        } catch (\ReflectionException) {
            $attributes = [];
        }

        return ArrayList::collect($attributes)
            ->map(static fn (\ReflectionAttribute $a) => $a->newInstance())
            ->toArray()
        ;
    }
}
