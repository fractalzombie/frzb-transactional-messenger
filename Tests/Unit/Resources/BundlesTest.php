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

namespace FRZB\Component\TransactionalMessenger\Tests\Unit\Resources;

use FRZB\Component\DependencyInjection\DependencyInjectionBundle;
use FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBus;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

#[Group('transactional-messenger')]
class BundlesTest extends TestCase
{
    public function testConfiguredBundles(): void
    {
        $bundles = require __DIR__.'/../../../Resources/bundles.php';
        $expectedBundles = [
            FrameworkBundle::class => ['all' => true],
            TransactionalMessageBus::class => ['all' => true],
        ];

        self::assertSame($expectedBundles, $bundles);
    }
}
