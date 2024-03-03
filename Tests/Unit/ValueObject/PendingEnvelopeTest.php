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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\ValueObject;

use FRZB\Component\TransactionalMessenger\Enum\CommitType;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use FRZB\Component\TransactionalMessenger\ValueObject\PendingEnvelope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('transactional-messenger')]
class PendingEnvelopeTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testConstructorMethod(object $message, array $commitTypes): void
    {
        $envelope = EnvelopeHelper::wrap($message);
        $whenPended = new \DateTimeImmutable();
        $pendingEnvelope = new PendingEnvelope($envelope, $whenPended);

        self::assertSame($whenPended, $pendingEnvelope->whenPended);
        self::assertSame(spl_object_hash($message), spl_object_hash($pendingEnvelope->envelope->getMessage()));
        self::assertSame($message::class, $pendingEnvelope->getMessageClass());
        self::assertTrue($pendingEnvelope->isTransactional(...$commitTypes));
    }

    public static function dataProvider(): iterable
    {
        yield 'TransactionalOnTerminateMessage with PendingEnvelope' => [
            'message' => new TransactionalOnTerminateMessage(),
            'commitTypes' => [CommitType::OnTerminate],
        ];

        yield 'TransactionalOnResponseMessage with PendingEnvelope' => [
            'message' => new TransactionalOnResponseMessage(),
            'commitTypes' => [CommitType::OnResponse],
        ];

        yield 'TransactionalOnHandledMessage with PendingEnvelope' => [
            'message' => new TransactionalOnHandledMessage(),
            'commitTypes' => [CommitType::OnHandled],
        ];
    }
}
