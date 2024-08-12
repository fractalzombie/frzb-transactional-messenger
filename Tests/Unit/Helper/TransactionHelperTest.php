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
            : self::assertEmpty(TransactionHelper::getTransactional($className));
    }

    #[DataProvider('dispatchableProvider')]
    public function getIsDispatchableMethod(string $className, bool $isAllowed, array $commitTypes): void
    {
        self::assertSame($isAllowed, TransactionHelper::isDispatchable($className, ...$commitTypes));
    }

    public static function transactionalProvider(): iterable
    {
        yield ClassHelper::getShortName(TransactionalOnTerminateMessage::class) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isTransactional' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnResponseMessage::class) => [
            'className' => TransactionalOnResponseMessage::class,
            'isTransactional' => true,
        ];

        yield ClassHelper::getShortName(TransactionalOnHandledMessage::class) => [
            'className' => TransactionalOnHandledMessage::class,
            'isTransactional' => true,
        ];

        yield ClassHelper::getShortName(NonTransactionalMessage::class) => [
            'className' => NonTransactionalMessage::class,
            'isTransactional' => false,
        ];

        yield 'InvalidClassName' => [
            'className' => 'InvalidClassName',
            'isTransactional' => false,
        ];
    }

    public function dispatchableProvider(): iterable
    {
        yield \sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isAllowed' => true,
            'commitTypes' => [
                CommitType::OnTerminate,
            ],
        ];

        yield \sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'className' => TransactionalOnResponseMessage::class,
            'isAllowed' => true,
            'commitTypes' => [
                CommitType::OnResponse,
            ],
        ];

        yield \sprintf('%s is allowed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'className' => TransactionalOnHandledMessage::class,
            'isAllowed' => true,
            'commitTypes' => [
                CommitType::OnHandled,
            ],
        ];

        yield \sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'className' => TransactionalOnTerminateMessage::class,
            'isAllowed' => false,
            'commitTypes' => [
                CommitType::OnResponse,
            ],
        ];

        yield \sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnResponseMessage::class)) => [
            'className' => TransactionalOnResponseMessage::class,
            'isAllowed' => false,
            'commitTypes' => [
                CommitType::OnTerminate,
            ],
        ];

        yield \sprintf('%s is not allowed', ClassHelper::getShortName(TransactionalOnHandledMessage::class)) => [
            'className' => TransactionalOnHandledMessage::class,
            'isAllowed' => false,
            'commitTypes' => [
                CommitType::OnTerminate,
            ],
        ];

        yield \sprintf('%s is not allowed', ClassHelper::getShortName(NonTransactionalMessage::class)) => [
            'className' => NonTransactionalMessage::class,
            'isAllowed' => false,
            'commitTypes' => [],
        ];

        yield 'InvalidClassName is not allowed' => [
            'className' => 'InvalidClassName',
            'isAllowed' => false,
            'commitTypes' => [],
        ];
    }
}
