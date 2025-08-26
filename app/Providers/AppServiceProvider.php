<?php

namespace App\Providers;

use App\Auth\CustomUserProvider;
use App\Listeners\SetUserLoggedOutOnLogout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('custom', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });
        
        // Register logout event listener
        Event::listen(Logout::class, SetUserLoggedOutOnLogout::class);
        
        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CleanupExpiredSessions::class,
            ]);
        }
    }
}
