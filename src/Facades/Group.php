<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Facades;

use Illuminate\Support\Facades\Facade;
use Yuxin\Feishu\HttpClient;

/**
 * @method static HttpClient getHttpClient()
 * @method static void setGuzzleOptions(array $options)
 * @method static string search(string $query, string $userIdType = 'open_id')
 *
 * @see \Yuxin\Feishu\Group
 */
class Group extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feishu.group';
    }
}
