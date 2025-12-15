<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CheckEntertainerImages::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // register role middleware alias
        if ($this->app->bound('router')) {
            $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
            if ($this->app->environment('local')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
