<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;
use Yuxin\Feishu\HttpClient;

interface MessageInterface
{
    public function getHttpClient(): HttpClient;

    public function setGuzzleOptions(array $options): void;

    public function send(
        string $to,
        string $messageType,
        string|array $content,
        string $userIdType = UserIDTypeEnum::OpenID->value,
        string $receiveIdType = ReceiveIDTypeEnum::OpenID->value,
    ): bool;
}
