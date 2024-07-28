Transactional Messenger Component
=============================

![Workflow Build Status](https://github.com/fractalzombie/frzb-transactional-messenger/actions/workflows/build.yml/badge.svg?event=push)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/?branch=main)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)
[![Build Status](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/badges/build.png?b=main)](https://scrutinizer-ci.com/g/fractalzombie/frzb-transactional-messenger/build-status/main)
[![Coverage Status](https://coveralls.io/repos/github/fractalzombie/frzb-transactional-messenger/badge.svg?branch=main)](https://coveralls.io/github/fractalzombie/frzb-transactional-messenger?branch=main)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=bugs)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=ncloc)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=coverage)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=fractalzombie_frzb-transactional-messenger&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=fractalzombie_frzb-transactional-messenger)

The `Transactional Messenger` component allows make messenger transactional

Installation
------------
The recommended way to install is through Composer:

```
composer require frzb/transactional-messenger
```

It requires PHP version 8.2 and higher.

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

Contributions
---------
![Alt](https://repobeats.axiom.co/api/embed/15b14d3e93a2c90b09ea6029f27f864f38ce0901.svg "Repobeats analytics image")
