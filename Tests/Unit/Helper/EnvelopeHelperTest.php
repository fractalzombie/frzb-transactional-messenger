<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Helper;

use FRZB\Component\TransactionalMessenger\Helper\ClassHelper;
use FRZB\Component\TransactionalMessenger\Helper\EnvelopeHelper;
use FRZB\Component\TransactionalMessenger\Tests\Stub\Message\TransactionalOnTerminateMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/** @iternal */
#[Group('transactional-messenger')]
final class EnvelopeHelperTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testWrapMethod(object $target): void
    {
        $envelope = EnvelopeHelper::wrap($target);
        $messageClass = $target instanceof Envelope ? $target->getMessage()::class : $target::class;

        self::assertInstanceOf(Envelope::class, $envelope);
        self::assertSame($messageClass, $envelope->getMessage()::class);
        self::assertNotNull($envelope->last(DispatchAfterCurrentBusStamp::class));
    }

    public function dataProvider(): iterable
    {
        yield sprintf('%s', ClassHelper::getShortName(TransactionalOnTerminateMessage::class)) => [
            'target' => new TransactionalOnTerminateMessage(),
            'has_attributes' => true,
        ];

        yield sprintf('%s', ClassHelper::getShortName(Envelope::class)) => [
            'target' => Envelope::wrap(new TransactionalOnTerminateMessage()),
            'has_attributes' => false,
        ];
    }
}
