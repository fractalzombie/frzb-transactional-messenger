<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Stub\ValueObject;

/** @internal */
final class TestObject
{
    public function getId(): string
    {
        return spl_object_hash($this);
    }
}
