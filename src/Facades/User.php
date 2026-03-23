<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Facades;

use Illuminate\Support\Facades\Facade;
use Yuxin\Feishu\HttpClient;

/**
 * @method static HttpClient getHttpClient()
 * @method static void setGuzzleOptions(array $options)
 * @method static string getId(string $username, string $type = 'union_id')
 *
 * @see \Yuxin\Feishu\User
 */
class User extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feishu.user';
    }
}
