<?php

declare(strict_types=1);

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    FRZB\Component\DependencyInjection\DependencyInjectionBundle::class => ['all' => true],
    FRZB\Component\TransactionalMessenger\MessageBus\TransactionalMessageBus::class => ['all' => true],
];
