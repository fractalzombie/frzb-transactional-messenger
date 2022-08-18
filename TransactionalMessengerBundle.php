<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TransactionalMessengerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new TransactionalMessengerExtension());
    }
}
