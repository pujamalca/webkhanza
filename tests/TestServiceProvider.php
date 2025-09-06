<?php

namespace Tests;

use App\Services\WebsiteThemeService;
use Tests\Mocks\MockWebsiteThemeService;
use Illuminate\Support\ServiceProvider;
use App\Models\WebsiteIdentity;

class TestServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Override WebsiteThemeService untuk testing
        $this->app->bind(WebsiteThemeService::class, function () {
            return new MockWebsiteThemeService();
        });
        
        // Override website.identity binding
        $this->app->bind('website.identity', function () {
            $identity = new WebsiteIdentity();
            $identity->nama_rs = 'Test Hospital';
            $identity->alamat_rs = 'Test Address';
            $identity->warna_primary = '#3b82f6';
            $identity->warna_secondary = '#64748b';
            $identity->name = 'Test Hospital';
            $identity->logo = null;
            $identity->favicon = null;
            
            return $identity;
        });
    }
    
    public function boot()
    {
        // Override cache untuk menggunakan array
        config(['cache.default' => 'array']);
        config(['cache.stores.array' => [
            'driver' => 'array',
            'serialize' => false,
        ]]);
    }
}