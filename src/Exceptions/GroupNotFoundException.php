<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Exceptions;

use Throwable;

class GroupNotFoundException extends Exception
{
    public function __construct(string $message = 'Group not found', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
