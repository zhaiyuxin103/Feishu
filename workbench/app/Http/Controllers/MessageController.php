<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Yuxin\Feishu\Enums\MessageTypeEnum;
use Yuxin\Feishu\Enums\ReceiveIDTypeEnum;
use Yuxin\Feishu\Enums\UserIDTypeEnum;

class MessageController
{
    public function store()
    {
        // 发送给用户
        app('feishu.message')->send('18816545428', MessageTypeEnum::Text->value, 'Hello, world!');

        // 发送给群组
        $group = app('feishu.group')->search('Chatbot');
        app('feishu.message')->send(
            $group,
            MessageTypeEnum::Text->value,
            'Hello, world!',
            UserIDTypeEnum::OpenID->value,
            ReceiveIDTypeEnum::ChatID->value,
        );
    }
}
