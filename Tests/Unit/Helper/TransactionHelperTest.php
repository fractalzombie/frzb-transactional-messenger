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
final class TransactionHelperTest extends TestCase
{
    #[DataProvider('transactionalProvider')]
    public function testIsTransactionalMethod(string $className, bool $isTransactional): void
    {
        self::assertSame($isTransactional, TransactionHelper::isTransactional($className));
    }

    #[DataProvider('transactionalDataProvider')]
    public function getTransactionalMethod(string $className, bool $isTransactional): void
    {
        $isTransactional
            ? self::assertNotEmpty(TransactionHelper::getTransactional($className))
            : self::assertEmpty(TransactionHelper::getTransactional($className))
        ;
    }

    #[DataProvider('dispatchableProvider')]
    public function getIsDispatchableMethod(string $className, bool $isAllowed, array $commitTypes): void
    {
        self::assertSame($isAllowed, TransactionHelper::isDispatchable($className, ...$commitTypes));
    }

    public static function transactionalProvider(): iterable
    {
        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'is_transactional' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'is_transactional' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'is_transactional' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'class_name' => NonTransactionalMessage::class,
            'is_transactional' => false,
        ];

        yield 'InvalidClassName' => [
            'class_name' => 'InvalidClassName',
            'is_transactional' => false,
        ];
    }

    public function dispatchableProvider(): iterable
    {
        yield sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'is_allowed' => true,
            'commit_types' => [
                CommitType::OnTerminate,
            ],
        ];

        yield sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'is_allowed' => true,
            'commit_types' => [
                CommitType::OnResponse,
            ],
        ];

        yield sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'is_allowed' => true,
            'commit_types' => [
                CommitType::OnHandled,
            ],
        ];

        yield sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'class_name' => TransactionalOnTerminateMessage::class,
            'is_allowed' => false,
            'commit_types' => [
                CommitType::OnResponse,
            ],
        ];

        yield sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'class_name' => TransactionalOnResponseMessage::class,
            'is_allowed' => false,
            'commit_types' => [
                CommitType::OnTerminate,
            ],
        ];

        yield sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'class_name' => TransactionalOnHandledMessage::class,
            'is_allowed' => false,
            'commit_types' => [
                CommitType::OnTerminate,
            ],
        ];

        yield sprintf('%s is not allowed', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'class_name' => NonTransactionalMessage::class,
            'is_allowed' => false,
            'commit_types' => [],
        ];

        yield 'InvalidClassName is not allowed' => [
            'class_name' => 'InvalidClassName',
            'is_allowed' => false,
            'commit_types' => [],
        ];
    }
}
