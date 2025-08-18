<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

interface AccessTokenInterface
{
    public function getAccessToken(): string;
}
