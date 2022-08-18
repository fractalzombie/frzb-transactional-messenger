<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Stub\ValueObject;

/** @internal */
final class MappedTestObject
{
    public function __construct(
        public readonly TestObject $testObject,
    ) {
    }

    public static function fromTestObject(TestObject $testObject): self
    {
        return new self($testObject);
    }
}
