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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Storage;

use FRZB\Component\TransactionalMessenger\Storage\Storage;
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
        $storage = new Storage();
        $item = new TestObject();

        $storage->append($item);

        self::assertSame($item->getId(), $storage->next()?->getId());
    }

    public function testPrependMethod(): void
    {
        $item1 = new TestObject();
        $item2 = new TestObject();
        $storage = new Storage([$item1]);

        $storage->prepend($item2);

        self::assertSame($item2->getId(), $storage->next()?->getId());
        self::assertSame($item1->getId(), $storage->next()?->getId());
    }

    public function testNextMethod(): void
    {
        self::assertNotNull((new Storage([new TestObject()]))->next());
    }

    public function testMapMethod(): void
    {
        $items = [new TestObject(), new TestObject(), new TestObject()];
        $storage = new Storage($items);

        $mappedStorage = $storage->map(static fn (TestObject $to) => MappedTestObject::fromTestObject($to));

        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
        self::assertInstanceOf(MappedTestObject::class, $mappedStorage->next());
    }

    public function testIterateMethod(): void
    {
        $items = [new TestObject(), new TestObject(), new TestObject()];
        $storage = new Storage($items);

        foreach ($storage->iterate() as $index => $item) {
            self::assertSame($items[$index]->getId(), $item->getId());
        }

        self::assertEmpty($storage->list());
    }

    public function testFilterMethod(): void
    {
        $item = new TestObject();
        $items = [new TestObject(), new TestObject(), $item];
        $storage = new Storage($items);

        $storage->init($storage->filter(static fn (TestObject $to) => $to->getId() === $item->getId()));

        self::assertSame($item->getId(), $storage->next()?->getId());
        self::assertNull($storage->next());
    }

    public function testMergeMethod(): void
    {
        $storage = new Storage([new TestObject(), new TestObject(), new TestObject()]);

        $storage->merge(new Storage([new TestObject(), new TestObject(), new TestObject()]));

        self::assertSame(6, $storage->count());
    }

    public function testClearMethod(): void
    {
        $storage = new Storage([new TestObject(), new TestObject(), new TestObject()]);

        $storage->clear();

        self::assertCount(0, $storage->list());
        self::assertEmpty($storage->list());
    }

    public function testListMethod(): void
    {
        $storage = new Storage([new TestObject(), new TestObject(), new TestObject()]);

        self::assertCount(3, $storage->list());
    }

    public function testCountMethod(): void
    {
        $storage = new Storage([new TestObject(), new TestObject(), new TestObject()]);

        self::assertSame(3, $storage->count());

        $storage->append(new TestObject());

        self::assertSame(4, $storage->count());
    }

    public function testInitMethod(): void
    {
        $storage = (new Storage())->init([new TestObject(), new TestObject(), new TestObject()]);

        self::assertCount(3, $storage->list());

        $storage->init(new Storage([new TestObject()]));

        self::assertCount(1, $storage->list());
    }
}
