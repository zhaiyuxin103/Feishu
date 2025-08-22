<?php

declare(strict_types=1);

namespace Yuxin\Feishu;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/feishu.php', 'feishu');

        $this->registerBindings();
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublications();
        }
    }

    private function registerBindings()
    {
        $this->registerAccessToken();
        $this->registerGroup();
        $this->registerMessage();
        $this->registerUser();
    }

    private function registerAccessToken()
    {
        $this->app->singleton(AccessToken::class, function () {
            return new AccessToken(
                config('feishu.app_id'),
                config('feishu.app_secret')
            );
        });

        $this->app->alias(AccessToken::class, 'feishu.access_token');
    }

    private function registerGroup()
    {
        $this->app->singleton(Group::class, function () {
            return new Group(
                config('feishu.app_id'),
                config('feishu.app_secret'),
                $this->app->make(AccessToken::class),
            );
        });

        $this->app->alias(Group::class, 'feishu.group');
    }

    private function registerMessage()
    {
        $this->app->singleton(Message::class, function () {
            return new Message(
                config('feishu.app_id'),
                config('feishu.app_secret'),
                $this->app->make(AccessToken::class),
            );
        });

        $this->app->alias(Message::class, 'feishu.message');
    }

    private function registerUser()
    {
        $this->app->singleton(User::class, function () {
            return new User(
                config('feishu.app_id'),
                config('feishu.app_secret'),
                $this->app->make(AccessToken::class),
            );
        });

        $this->app->alias(User::class, 'feishu.user');
    }

    private function registerPublications()
    {
        $this->publishes([
            __DIR__ . '/../config/feishu.php' => $this->app->configPath('feishu.php'),
        ], ['feishu', 'feishu-config']);
    }
}
