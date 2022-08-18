<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class ClassHelper
{
    final public const DEFAULT_SHORT_NAME = 'NoName';

    private function __construct()
    {
    }

    public static function getShortName(string|object $target): string
    {
        try {
            return (new \ReflectionClass($target))->getShortName();
        } catch (\ReflectionException) {
            return self::DEFAULT_SHORT_NAME;
        }
    }
}
