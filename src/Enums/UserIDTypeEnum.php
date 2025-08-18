<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Enums;

enum UserIDTypeEnum: string
{
    case UnionID = 'union_id';
    case OpenID  = 'open_id';
    case UserID  = 'user_id';
}
