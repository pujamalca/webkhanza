<?php

namespace App\Providers;

use App\Services\WebsiteThemeService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WebsiteThemeService::class, function ($app) {
            return new WebsiteThemeService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share theme service dengan semua views
        View::composer('*', function ($view) {
            $view->with('themeService', app(WebsiteThemeService::class));
        });
    }
}
