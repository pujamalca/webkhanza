<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebsiteIdentity;

class CoreDataSeeder extends Seeder
{
    /**
     * Seed core data yang diperlukan aplikasi.
     * Seeder ini akan selalu dijalankan untuk memastikan data essential tersedia.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding core application data...');

        $this->seedWebsiteIdentity();
        $this->clearCaches();

        $this->command->info('✅ Core data seeded successfully!');
    }

    /**
     * Seed website identity data
     */
    private function seedWebsiteIdentity(): void
    {
        // Gunakan singleton pattern dari model untuk memastikan hanya ada satu data
        try {
            $websiteIdentity = WebsiteIdentity::getInstance();
            
            if ($websiteIdentity->wasRecentlyCreated) {
                $this->command->info('✓ Website identity created successfully');
            } else {
                $this->command->info('✓ Website identity already exists');
            }

            // Pastikan data lengkap - hanya gunakan kolom yang ada
            if (empty($websiteIdentity->name) || $websiteIdentity->name === 'WebKhanza') {
                $websiteIdentity->update([
                    'name' => 'WebKhanza',
                    'tagline' => 'Sistem Informasi Kesehatan Terpadu',
                    'description' => 'Platform terintegrasi untuk manajemen rumah sakit, klinik, dan fasilitas kesehatan dengan teknologi modern dan user-friendly interface.',
                    'address' => 'Jl. Kesehatan Raya No. 123, Jakarta Pusat, DKI Jakarta 10110, Indonesia',
                    'phone' => '+62 21 8012-3456',
                    'email' => 'info@webkhanza.com',
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#1e40af',
                    'accent_color' => '#dc2626',
                ]);
                $this->command->info('✓ Website identity data updated');
            }

            // Tampilkan info website identity
            $this->command->table(
                ['Field', 'Value'],
                [
                    ['Name', $websiteIdentity->name],
                    ['Tagline', $websiteIdentity->tagline],
                    ['Email', $websiteIdentity->email],
                    ['Phone', $websiteIdentity->phone],
                    ['Primary Color', $websiteIdentity->primary_color],
                ]
            );

        } catch (\Exception $e) {
            $this->command->error('❌ Failed to seed website identity: ' . $e->getMessage());
            
            // Fallback: coba buat manual
            try {
                WebsiteIdentity::create([
                    'name' => 'WebKhanza',
                    'tagline' => 'Sistem Informasi Kesehatan Terpadu',
                    'description' => 'Platform terintegrasi untuk manajemen rumah sakit dan fasilitas kesehatan.',
                    'address' => 'Jakarta, Indonesia',
                    'phone' => '+62 21 1234567',
                    'email' => 'info@webkhanza.com',
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#1e40af',
                    'accent_color' => '#dc2626'
                ]);
                $this->command->info('✓ Website identity created via fallback method');
            } catch (\Exception $fallbackException) {
                $this->command->error('❌ Fallback creation also failed: ' . $fallbackException->getMessage());
            }
        }
    }

    /**
     * Clear relevant caches
     */
    private function clearCaches(): void
    {
        try {
            // Clear theme cache if theme service exists
            if (app()->bound(\App\Services\WebsiteThemeService::class)) {
                app(\App\Services\WebsiteThemeService::class)->clearCache();
                $this->command->info('✓ Theme cache cleared');
            }

            // Clear config cache
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            $this->command->info('✓ Config cache cleared');
            
        } catch (\Exception $e) {
            $this->command->warn('⚠ Cache clearing failed: ' . $e->getMessage());
        }
    }
}