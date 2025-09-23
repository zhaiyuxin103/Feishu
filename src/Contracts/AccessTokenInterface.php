<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

use Yuxin\Feishu\HttpClient;

interface AccessTokenInterface
{
    public function getKey(): string;

    public function getHttpClient(): HttpClient;

    public function getAccessToken(): string;

    public function setGuzzleOptions(array $options): void;

    public function getToken(): string;
}
