<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Enums;

enum ReceiveIDTypeEnum: string
{
    case UnionID = 'union_id';
    case OpenID  = 'open_id';
    case Email   = 'email';
    case ChatID  = 'chat_id';
    case UserID  = 'user_id';
}
