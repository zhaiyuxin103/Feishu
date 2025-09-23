<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class Feishu
{
    public function __construct(protected Application $app) {}

    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }

        throw new InvalidArgumentException("Method [{$method}] does not exist on FeishuManager.");
    }

    public function accessToken(): AccessToken
    {
        return new AccessToken(
            config('feishu.app_id'),
            config('feishu.app_secret'),
            $this->app->make('cache.store'),
            $this->app->make(HttpClient::class),
        );
    }

    public function group(): Group
    {
        return new Group(
            config('feishu.app_id'),
            config('feishu.app_secret'),
            $this->accessToken(),
            null,
            $this->app->make(HttpClient::class),
        );
    }

    public function message(): Message
    {
        return new Message(
            config('feishu.app_id'),
            config('feishu.app_secret'),
            $this->accessToken(),
            null,
            $this->app->make(HttpClient::class),
        );
    }

    public function user(): User
    {
        return new User(
            config('feishu.app_id'),
            config('feishu.app_secret'),
            $this->accessToken(),
            $this->app->make(HttpClient::class),
        );
    }
}
