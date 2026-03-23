<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Facades;

use Illuminate\Support\Facades\Facade;
use Yuxin\Feishu\AccessToken;
use Yuxin\Feishu\Group;
use Yuxin\Feishu\Message;
use Yuxin\Feishu\User;

/**
 * @method static AccessToken accessToken()
 * @method static Group group()
 * @method static Message message()
 * @method static User user()
 *
 * @see \Yuxin\Feishu\Feishu
 */
class Feishu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feishu';
    }
}
