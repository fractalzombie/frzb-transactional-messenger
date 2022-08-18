<?php

namespace FRZB\Component\TransactionalMessenger\Enum;

enum CommitType
{
    case OnTerminate;
    case OnResponse;
    case OnHandled;
}
