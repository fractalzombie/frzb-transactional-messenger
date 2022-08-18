<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Storage;

use FRZB\Component\TransactionalMessenger\Storage\Storage as StorageImpl;
use FRZB\Component\TransactionalMessenger\Tests\Stub\ValueObject\MappedTestObject;
use FRZB\Component\TransactionalMessenger\Tests\Stub\ValueObject\TestObject;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/** @internal */
#[Group('transactional-messenger')]
final class StorageTest extends TestCase
{
    public function testAppendMethod(): void
    {
        $storage = new StorageImpl();
        $item = new TestObject();

        $storage->append($item);

        self::assertSame($item->getId(), $storage->next()?->getId());
    }

    public function testPrependMethod(): void
    {
        $item1 = new TestObject();
        $item2 = new TestObject();
        $storage = new StorageImpl([$item1]);

        $storage->prepend($item2);

        self::assertSame($item2->getId(), $storage->next()?->getId());
        self::assertSame($item1->getId(), $storage->next()?->getId());
    }

    public function testNextMethod(): void
    {
        self::assertNotNull((new StorageImpl([new TestObject()]))->next());
    }

    public function testMapMethod(): void
    {
        $items = [new TestObject(), new TestObject(), new TestObject()];
        $storage = new StorageImpl($items);

        $mappedStorage = $storage->map(static fn (TestObject $to) => MappedTestObject::fromTestObject($to));

        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
    }

    public function testFilterMethod(): void
    {
        $item = new TestObject();
        $items = [new TestObject(), new TestObject(), $item];
        $storage = new StorageImpl($items);

        $storage->init($storage->filter(static fn (TestObject $to) => $to->getId() === $item->getId()));

        self::assertSame($item->getId(), $storage->next()?->getId());
        self::assertNull($storage->next());
    }

    public function testMergeMethod(): void
    {
        $storage = new StorageImpl([new TestObject(), new TestObject(), new TestObject()]);

        $storage->merge(new StorageImpl([new TestObject(), new TestObject(), new TestObject()]));

        self::assertSame(6, $storage->count());
    }

    public function testClearMethod(): void
    {
        $storage = new StorageImpl([new TestObject(), new TestObject(), new TestObject()]);

        $storage->clear();

        self::assertCount(0, $storage->list());
        self::assertEmpty($storage->list());
    }

    public function testListMethod(): void
    {
        $storage = new StorageImpl([new TestObject(), new TestObject(), new TestObject()]);

        self::assertCount(3, $storage->list());
    }

    public function testCountMethod(): void
    {
        $storage = new StorageImpl([new TestObject(), new TestObject(), new TestObject()]);

        self::assertSame(3, $storage->count());

        $storage->append(new TestObject());

        self::assertSame(4, $storage->count());
    }

    public function testInitMethod(): void
    {
        $storage = (new StorageImpl())->init([new TestObject(), new TestObject(), new TestObject()]);

        self::assertCount(3, $storage->list());

        $storage->init(new StorageImpl([new TestObject()]));

        self::assertCount(1, $storage->list());
    }
}
