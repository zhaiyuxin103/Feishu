<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Facades;

use Illuminate\Support\Facades\Facade;
use Yuxin\Feishu\HttpClient;

/**
 * @method static HttpClient getHttpClient()
 * @method static void setGuzzleOptions(array $options)
 * @method static string getKey()
 * @method static string getToken()
 * @method static string getAccessToken()
 *
 * @see \Yuxin\Feishu\AccessToken
 */
class AccessToken extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feishu.access_token';
    }
}
