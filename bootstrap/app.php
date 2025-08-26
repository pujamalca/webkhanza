<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'single.device' => \App\Http\Middleware\SingleDeviceLogin::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeoutHandler::class,
            'session.cleanup' => \App\Http\Middleware\CleanupSessionOnLogout::class,
        ]);
        
        // Add session timeout handler to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\SessionTimeoutHandler::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
