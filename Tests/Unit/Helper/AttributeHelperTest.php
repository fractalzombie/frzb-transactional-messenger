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
use FRZB\Component\TransactionalMessenger\Helper\AttributeHelper;
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
final class AttributeHelperTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testGetAttributesMethod(string $className, bool $hasAttributes): void
    {
        $hasAttributes
            ? self::assertNotEmpty(AttributeHelper::getAttributes($className, Transactional::class))
            : self::assertEmpty(AttributeHelper::getAttributes($className, Transactional::class));
    }

    #[DataProvider('dataProvider')]
    public function testGetAttributeMethod(string $className, bool $hasAttributes): void
    {
        $hasAttributes
            ? self::assertNotNull(AttributeHelper::getAttribute($className, Transactional::class))
            : self::assertNull(AttributeHelper::getAttribute($className, Transactional::class));
    }

    #[DataProvider('dataProvider')]
    public function testGetReflectionAttributesMethod(string $className, bool $hasAttributes): void
    {
        $hasAttributes
            ? self::assertNotEmpty(AttributeHelper::getReflectionAttributes($className, Transactional::class))
            : self::assertEmpty(AttributeHelper::getReflectionAttributes($className, Transactional::class));
    }

    #[DataProvider('dataProvider')]
    public function testHasAttributeMethod(string $className, bool $hasAttributes): void
    {
        self::assertSame($hasAttributes, AttributeHelper::hasAttribute($className, Transactional::class));
    }

    public static function dataProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'has_attributes' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'has_attributes' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'has_attributes' => true,
        ];

        yield ClassHelper::getShortName(ExtendedTransactionalMessage::class) => [
            'class_name' => ExtendedTransactionalMessage::class,
            'has_attributes' => true,
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'class_name' => NonTransactionalMessage::class,
            'has_attributes' => false,
        ];

        yield 'InvalidClassName' => [
            'class_name' => 'InvalidClassName',
            'has_attributes' => false,
        ];
    }
}
