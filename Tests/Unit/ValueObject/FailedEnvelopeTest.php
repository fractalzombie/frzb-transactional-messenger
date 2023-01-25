<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\ValueObject;

use FRZB\Component\TransactionalMessenger\Exception\DispatchException;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnHandledMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnResponseMessage;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use FRZB\Component\TransactionalMessenger\ValueObject\FailedEnvelope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('transactional-messenger')]
class FailedEnvelopeTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testConstructorMethod(object $message): void
    {
        $envelope = EnvelopeHelper::wrap($message);
        $whenFailed = new \DateTimeImmutable();
        $exception = new DispatchException('Something goes wrong');
        $failedEnvelope = new FailedEnvelope($envelope, $exception, $whenFailed);

        self::assertSame($whenFailed, $failedEnvelope->whenFailed);
        self::assertSame(spl_object_hash($exception), spl_object_hash($failedEnvelope->exception));
        self::assertSame(spl_object_hash($message), spl_object_hash($failedEnvelope->envelope->getMessage()));
    }

    public static function dataProvider(): iterable
    {
        yield 'TransactionalOnTerminateMessage with FailedEnvelope' => [
            'message' => new TransactionalOnTerminateMessage(),
        ];

        yield 'TransactionalOnResponseMessage with FailedEnvelope' => [
            'message' => new TransactionalOnResponseMessage(),
        ];

        yield 'TransactionalOnHandledMessage with FailedEnvelope' => [
            'message' => new TransactionalOnHandledMessage(),
        ];
    }
}
