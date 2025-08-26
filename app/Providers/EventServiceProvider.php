<?php

namespace App\Providers;

use App\Listeners\CleanupSessionsOnLogout;
use App\Listeners\SetUserLoggedOutOnLogout;
use App\Listeners\LogUserActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Logout::class => [
            SetUserLoggedOutOnLogout::class,
            CleanupSessionsOnLogout::class,
        ],
    ];

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $subscribe = [
        LogUserActivity::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
