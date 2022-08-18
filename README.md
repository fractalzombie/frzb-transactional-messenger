Transactional Messenger Component
=============================

![Workflow Build Status](https://github.com/fractalzombie/frzb-transactional-messenger/actions/workflows/ci.yml/badge.svg?event=push)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/?branch=main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![Build Status](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/build.png?b=main)](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/build-status/main)
[![Coverage Status](https://coveralls.io/repos/github/fractalzombie/frzb-transactional-messenger/badge.svg?branch=main)](https://coveralls.io/github/fractalzombie/frzb-transactional-messenger?branch=main)

The `Transactional Messenger` component allows make messenger transactional

Installation
------------
The recommended way to install is through Composer:

```
composer require frzb/transactional-messenger
```

It requires PHP version 8.1 and higher.

Usage `#[Transactional]`
-----
`#[Transactional]` will automatically create and close transaction for your messages,
By default `CommitType` is `CommitType::OnTerminate`

CommitTypes
------------
 * `CommitType::OnTerminate` for requests, executes when response is sent without exceptions
 * `CommitType::OnResponse` for requests, executes when request end without exceptions
 * `CommitType::onHandled` for consumers, executes when message successfully handled

Events
-------
 * `FRZB\Component\TransactionalMessenger\Event\DispatchSucceedEvent` executes when message is dispatched
 * `FRZB\Component\TransactionalMessenger\Event\DispatchFailedEvent` executes when message is failure

Example
-------
```php
<?php

use \FRZB\Component\TransactionalMessenger\Attribute\Transactional;

#[Transactional]
final class CreateUserMessage {
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
```

Resources
---------
* [License](https://github.com/fractalzombie/frzb-transactional-messenger/blob/main/LICENSE.md)
