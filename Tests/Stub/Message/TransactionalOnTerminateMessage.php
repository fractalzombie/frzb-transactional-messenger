<?php

declare(strict_types=1);

namespace FRZB\Component\TransactionalMessenger\Tests\Stub\Message;

use FRZB\Component\TransactionalMessenger\Attribute\Transactional;
use FRZB\Component\TransactionalMessenger\Enum\CommitType;

/** @internal */
#[Transactional(CommitType::OnTerminate)]
final class TransactionalOnTerminateMessage
{
}
