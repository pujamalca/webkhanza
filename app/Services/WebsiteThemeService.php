<?php

namespace App\Services;

use App\Models\WebsiteIdentity;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk mengelola tema dan warna website
 */
class WebsiteThemeService
{
    /**
     * Cache key untuk website identity
     */
    const CACHE_KEY = 'website_identity_theme';
    
    /**
     * Cache duration dalam menit
     */
    const CACHE_DURATION = 60;

    /**
     * Mendapatkan data tema website dari cache atau database
     * 
     * @return WebsiteIdentity
     */
    public function getWebsiteIdentity(): WebsiteIdentity
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return WebsiteIdentity::getInstance();
        });
    }

    /**
     * Mendapatkan warna utama website
     * 
     * @return string
     */
    public function getPrimaryColor(): string
    {
        return $this->getWebsiteIdentity()->primary_color ?? '#3B82F6';
    }

    /**
     * Mendapatkan warna sekunder website
     * 
     * @return string
     */
    public function getSecondaryColor(): string
    {
        return $this->getWebsiteIdentity()->secondary_color ?? '#1E40AF';
    }

    /**
     * Mendapatkan warna aksen website
     * 
     * @return string
     */
    public function getAccentColor(): string
    {
        return $this->getWebsiteIdentity()->accent_color ?? '#EF4444';
    }

    /**
     * Mendapatkan semua warna tema sebagai array
     * 
     * @return array
     */
    public function getAllColors(): array
    {
        $identity = $this->getWebsiteIdentity();
        
        return [
            'primary' => $identity->primary_color ?? '#3B82F6',
            'secondary' => $identity->secondary_color ?? '#1E40AF',
            'accent' => $identity->accent_color ?? '#EF4444',
        ];
    }

    /**
     * Generate CSS variables untuk tema
     * 
     * @return string
     */
    public function generateCssVariables(): string
    {
        $colors = $this->getAllColors();
        
        $css = ':root {';
        $css .= '--color-primary: ' . $colors['primary'] . ';';
        $css .= '--color-secondary: ' . $colors['secondary'] . ';';
        $css .= '--color-accent: ' . $colors['accent'] . ';';
        
        // Generate RGB versions for transparency
        $css .= '--color-primary-rgb: ' . $this->hexToRgb($colors['primary']) . ';';
        $css .= '--color-secondary-rgb: ' . $this->hexToRgb($colors['secondary']) . ';';
        $css .= '--color-accent-rgb: ' . $this->hexToRgb($colors['accent']) . ';';
        
        $css .= '}';
        
        return $css;
    }

    /**
     * Konversi hex ke RGB
     * 
     * @param string $hex
     * @return string
     */
    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "$r, $g, $b";
    }

    /**
     * Clear theme cache
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Mendapatkan array warna untuk Filament theme
     * 
     * @return array
     */
    public function getFilamentColors(): array
    {
        return [
            'primary' => $this->getPrimaryColor(),
            'secondary' => $this->getSecondaryColor(),
            'danger' => $this->getAccentColor(),
        ];
    }
}