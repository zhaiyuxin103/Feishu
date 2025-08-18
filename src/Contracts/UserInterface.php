<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

interface UserInterface
{
    public function getId(string $username, string $type = 'union_id'): string;
}
