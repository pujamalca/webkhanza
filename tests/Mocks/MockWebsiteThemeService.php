<?php

namespace Tests\Mocks;

use App\Models\WebsiteIdentity;

/**
 * Mock service untuk testing yang tidak menggunakan cache database
 */
class MockWebsiteThemeService
{
    public function getWebsiteIdentity(): WebsiteIdentity
    {
        // Return mock instance tanpa database call
        $identity = new WebsiteIdentity();
        $identity->nama_rs = 'Test Hospital';
        $identity->alamat_rs = 'Test Address';
        $identity->warna_primary = '#3b82f6';
        $identity->warna_secondary = '#64748b';
        
        return $identity;
    }

    public function getPrimaryColor(): string
    {
        return '#3b82f6';
    }

    public function getSecondaryColor(): string
    {
        return '#64748b';
    }

    public function getAllColors(): array
    {
        return [
            'primary' => '#3b82f6',
            'secondary' => '#64748b',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
        ];
    }

    public function getCustomCSS(): string
    {
        return '';
    }

    public function clearCache(): void
    {
        // No-op untuk testing
    }
}