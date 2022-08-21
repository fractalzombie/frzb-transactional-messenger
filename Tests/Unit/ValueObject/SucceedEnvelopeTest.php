<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\ValueObject;

use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use FRZB\Component\TransactionalMessenger\ValueObject\SucceedEnvelope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('transactional-messenger')]
class SucceedEnvelopeTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testConstructorMethod(object $message): void
    {
        $envelope = EnvelopeHelper::wrap($message);
        $whenDispatched = new \DateTimeImmutable();
        $failedEnvelope = new SucceedEnvelope($envelope, $whenDispatched);

        self::assertSame($whenDispatched, $failedEnvelope->whenDispatched);
        self::assertSame(spl_object_hash($message), spl_object_hash($failedEnvelope->envelope->getMessage()));
    }

    public function dataProvider(): iterable
    {
        yield 'TransactionalOnTerminateMessage with SucceedEnvelope' => [
            'message' => new TransactionalOnTerminateMessage(),
        ];

        yield 'TransactionalOnResponseMessage with SucceedEnvelope' => [
            'message' => new TransactionalOnResponseMessage(),
        ];

        yield 'TransactionalOnHandledMessage with SucceedEnvelope' => [
            'message' => new TransactionalOnHandledMessage(),
        ];
    }
}
