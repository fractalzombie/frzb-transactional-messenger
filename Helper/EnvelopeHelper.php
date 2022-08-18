<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Helper;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/** @internal */
#[Immutable]
final class EnvelopeHelper
{
    private function __construct()
    {
    }

    public static function wrap(object $message): Envelope
    {
        $envelope = Envelope::wrap($message);
        $hasDelayedStamp = null !== $envelope->last(DispatchAfterCurrentBusStamp::class);

        return $hasDelayedStamp ? $envelope : $envelope->with(new DispatchAfterCurrentBusStamp());
    }
}
