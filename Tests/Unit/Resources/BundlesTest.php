<?php

declare(strict_types=1);

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
            DependencyInjectionBundle::class => ['all' => true],
            TransactionalMessageBus::class => ['all' => true],
        ];

        self::assertSame($expectedBundles, $bundles);
    }
}
