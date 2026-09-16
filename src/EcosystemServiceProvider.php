<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem;

use Illuminate\Support\ServiceProvider;
use Pr4w\Ecosystem\Console\ListCommand;

class EcosystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ecosystem.php', 'ecosystem');

        $this->app->singleton(Ecosystem::class, fn ($app) => new Ecosystem($app['config']));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ecosystem.php' => config_path('ecosystem.php'),
            ], 'ecosystem-config');

            $this->commands([ListCommand::class]);
        }
    }
}
