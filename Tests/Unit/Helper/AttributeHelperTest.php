<?php

declare(strict_types=1);


namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Helper;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\TransactionHelper;
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
    public function testGetShortNameMethod(string $className, string $shortClassName): void
    {
        self::assertSame($shortClassName, ClassHelper::getShortName($className));
    }

    public function dataProvider(): iterable
    {
        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'short_class_name' => 'TransactionalOnTerminateMessage',
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'short_class_name' => 'TransactionalOnResponseMessage',
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'short_class_name' => 'TransactionalOnHandledMessage',
        ];

        yield sprintf('%s', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'class_name' => NonTransactionalMessage::class,
            'short_class_name' => 'NonTransactionalMessage',
        ];

        yield 'InvalidClassName' => [
            'class_name' => 'InvalidClassName',
            'short_class_name' => 'InvalidClassName',
        ];
    }
}
