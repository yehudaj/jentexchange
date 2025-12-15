<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Create and configure the application, then register any local providers
 * that need to be available during this bootstrap flow.
 */
 $app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(App\Http\Middleware\TrustProxies::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

// Register the AuthServiceProvider if it exists so policies get registered.
if (class_exists(\App\Providers\AuthServiceProvider::class)) {
    $app->register(\App\Providers\AuthServiceProvider::class);
}

return $app;
