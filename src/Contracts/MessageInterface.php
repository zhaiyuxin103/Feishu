<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Contracts;

use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

interface MessageInterface
{
    public function send(
        string $to,
        string $messageType,
        string $content,
        string $userIdType = UserIDTypeEnum::OpenID->value,
        string $receiveIdType = ReceiveIDTypeEnum::OpenID->value,
    ): bool;
}
