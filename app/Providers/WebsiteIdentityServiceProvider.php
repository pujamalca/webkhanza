<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\WebsiteIdentity;

class WebsiteIdentityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register website identity as singleton
        $this->app->singleton('website.identity', function () {
            try {
                return WebsiteIdentity::getInstance();
            } catch (\Exception $e) {
                // Return default values if no data found
                return (object) [
                    'name' => 'WebKhanza',
                    'description' => 'Sistem Manajemen Pegawai',
                    'tagline' => 'Sistem Terpadu untuk Manajemen Pegawai',
                    'email' => 'admin@webkhanza.com',
                    'phone' => '',
                    'address' => '',
                    'logo' => null,
                    'favicon' => null,
                ];
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share website identity data with all views
        View::composer('*', function ($view) {
            $identity = app('website.identity');
            $view->with([
                'websiteIdentity' => $identity,
                'websiteName' => $identity->name ?? 'WebKhanza',
                'websiteDescription' => $identity->description ?? 'Sistem Manajemen Pegawai',
                'websiteTagline' => $identity->tagline ?? 'Sistem Terpadu untuk Manajemen Pegawai',
                'websiteLogo' => $identity->logo ? asset('storage/' . $identity->logo) : null,
                'websiteFavicon' => $identity->favicon ? asset('storage/' . $identity->favicon) : asset('favicon.ico'),
            ]);
        });
        
        // Add helper functions
        if (! function_exists('website_identity')) {
            function website_identity($key = null) {
                $identity = app('website.identity');
                
                if ($key === null) {
                    return $identity;
                }
                
                return $identity->$key ?? null;
            }
        }
    }
}
