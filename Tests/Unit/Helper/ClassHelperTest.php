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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Helper;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\ExtendedTransactionalMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\NonTransactionalMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @iternal */
#[Group('transactional-messenger')]
final class ClassHelperTest extends TestCase
{
    #[DataProvider('shortNameProvider')]
    public function testGetShortNameMethod(string $className, string $shortClassName): void
    {
        self::assertSame($shortClassName, ClassHelper::getShortName($className));
    }

    #[DataProvider('reflectionProvider')]
    public function testGetReflectionClassMethod(string $className, bool $isNull): void
    {
        $isNull
            ? self::assertNull(ClassHelper::getReflectionClass($className))
            : self::assertNotNull(ClassHelper::getReflectionClass($className));
    }

    #[DataProvider('parentReflectionProvider')]
    public function testGetParentReflectionClassMethod(string $className, bool $isNull): void
    {
        $isNull
            ? self::assertNull(ClassHelper::getParentReflectionClass($className))
            : self::assertNotNull(ClassHelper::getParentReflectionClass($className));
    }

    #[DataProvider('reflectionAttributesProvider')]
    public function testGetReflectionAttributesClassMethod(string $className, bool $isEmpty): void
    {
        $isEmpty
            ? self::assertEmpty(ClassHelper::getReflectionAttributes($className, Transactional::class))
            : self::assertNotEmpty(ClassHelper::getReflectionAttributes($className, Transactional::class));
    }

    public static function shortNameProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'className' => TransactionalOnTerminateMessage::class,
            'shortClassName' => 'TransactionalOnTerminateMessage',
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'className' => TransactionalOnResponseMessage::class,
            'shortClassName' => 'TransactionalOnResponseMessage',
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'className' => TransactionalOnHandledMessage::class,
            'shortClassName' => 'TransactionalOnHandledMessage',
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'className' => NonTransactionalMessage::class,
            'shortClassName' => 'NonTransactionalMessage',
        ];

        yield 'InvalidClassName' => [
            'className' => 'InvalidClassName',
            'shortClassName' => 'InvalidClassName',
        ];
    }

    public static function reflectionProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isNull' => false,
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'className' => TransactionalOnResponseMessage::class,
            'isNull' => false,
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'className' => TransactionalOnHandledMessage::class,
            'isNull' => false,
        ];

        yield ClassHelper::getShortName(ExtendedTransactionalMessage::class) => [
            'className' => ExtendedTransactionalMessage::class,
            'isNull' => false,
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'className' => NonTransactionalMessage::class,
            'isNull' => false,
        ];

        yield 'InvalidClassName' => [
            'className' => 'InvalidClassName',
            'isNull' => true,
        ];
    }

    public static function parentReflectionProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isNull' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'className' => TransactionalOnResponseMessage::class,
            'isNull' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'className' => TransactionalOnHandledMessage::class,
            'isNull' => true,
        ];

        yield ClassHelper::getShortName(ExtendedTransactionalMessage::class) => [
            'className' => ExtendedTransactionalMessage::class,
            'isNull' => false,
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'className' => NonTransactionalMessage::class,
            'isNull' => true,
        ];

        yield 'InvalidClassName' => [
            'className' => 'InvalidClassName',
            'isNull' => true,
        ];
    }

    public static function reflectionAttributesProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isEmpty' => false,
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'className' => TransactionalOnResponseMessage::class,
            'isEmpty' => false,
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'className' => TransactionalOnHandledMessage::class,
            'isEmpty' => false,
        ];

        yield ClassHelper::getShortName(ExtendedTransactionalMessage::class) => [
            'className' => ExtendedTransactionalMessage::class,
            'isEmpty' => true,
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'className' => NonTransactionalMessage::class,
            'isEmpty' => true,
        ];

        yield 'InvalidClassName' => [
            'className' => 'InvalidClassName',
            'isEmpty' => true,
        ];
    }
}
