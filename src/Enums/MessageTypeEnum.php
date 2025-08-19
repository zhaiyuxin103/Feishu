<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Enums;

enum MessageTypeEnum: string
{
    case Text        = 'text';
    case Image       = 'image';
    case File        = 'file';
    case Post        = 'post';
    case Audio       = 'audio';
    case Media       = 'media';
    case Sticker     = 'sticker';
    case Interactive = 'interactive';
    case ShareChat   = 'share_chat';
    case ShareUser   = 'share_user';
    case System      = 'system';
}
