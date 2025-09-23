<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

use Yuxin\Feishu\HttpClient;

interface UserInterface
{
    public function getHttpClient(): HttpClient;

    public function setGuzzleOptions(array $options): void;

    public function getId(string $username, string $type = 'union_id'): string;
}
