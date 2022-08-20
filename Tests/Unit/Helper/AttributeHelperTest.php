<?php

declare(strict_types=1);

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

    public function dataProvider(): iterable
    {
        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'has_attributes' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'has_attributes' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'has_attributes' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(ExtendedTransactionalMessage::class)) => [
            'class_name' => ExtendedTransactionalMessage::class,
            'has_attributes' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'class_name' => NonTransactionalMessage::class,
            'has_attributes' => false,
        ];

        yield 'InvalidClassName' => [
            'class_name' => 'InvalidClassName',
            'has_attributes' => false,
        ];
    }
}
