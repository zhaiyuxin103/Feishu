<?php

declare(strict_types=1);

namespace Yuxin\Feishu\Facades;

use Illuminate\Support\Facades\Facade;
use Yuxin\Feishu\HttpClient;

/**
 * @method static HttpClient getHttpClient()
 * @method static void setGuzzleOptions(array $options)
 * @method static bool send(string $to, string $messageType, string|array $content, string $userIdType = 'open_id', string $receiveIdType = 'open_id')
 *
 * @see \Yuxin\Feishu\Message
 */
class Message extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'feishu.message';
    }
}
