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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ecosystem');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ecosystem.php' => config_path('ecosystem.php'),
            ], 'ecosystem-config');

            // Publier les vues pour les réécrire au design du site.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/ecosystem'),
            ], 'ecosystem-views');

            // Le composant Vue, pour les sites Inertia.
            $this->publishes([
                __DIR__.'/../resources/js' => resource_path('js/ecosystem'),
            ], 'ecosystem-vue');

            $this->commands([ListCommand::class]);
        }
    }
}
