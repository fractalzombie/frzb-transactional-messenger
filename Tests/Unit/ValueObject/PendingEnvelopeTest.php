<?php

declare(strict_types=1);

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
