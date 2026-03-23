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
        $this->registerFeishu();
    }

    private function registerFeishu()
    {
        $this->app->singleton('feishu', fn ($app) => new Feishu($app));
        $this->app->singleton(HttpClient::class, fn () => new HttpClient);

        $this->app->singleton('feishu.access_token', fn ($app) => $app->make('feishu')->accessToken());
        $this->app->singleton('feishu.message', fn ($app) => $app->make('feishu')->message());
        $this->app->singleton('feishu.group', fn ($app) => $app->make('feishu')->group());
        $this->app->singleton('feishu.user', fn ($app) => $app->make('feishu')->user());

        $this->app->alias('feishu', Feishu::class);
        $this->app->alias('feishu.access_token', AccessToken::class);
        $this->app->alias('feishu.message', Message::class);
        $this->app->alias('feishu.group', Group::class);
        $this->app->alias('feishu.user', User::class);
    }

    private function registerPublications()
    {
        $this->publishes([
            __DIR__ . '/../config/feishu.php' => $this->app->configPath('feishu.php'),
        ], ['feishu', 'feishu-config']);
    }
}
