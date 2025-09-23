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
    }

    private function registerPublications()
    {
        $this->publishes([
            __DIR__ . '/../config/feishu.php' => $this->app->configPath('feishu.php'),
        ], ['feishu', 'feishu-config']);
    }
}
