<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebsiteIdentity;

class WebsiteIdentitySeeder extends Seeder
{
    public function run(): void
    {
        // Check if website identity already exists
        if (WebsiteIdentity::count() > 0) {
            $this->command->info('Website identity already exists, skipping...');
            return;
        }

        // Create default website identity
        WebsiteIdentity::create([
            'name' => 'WebKhanza',
            'tagline' => 'Sistem Informasi Kesehatan',
            'description' => 'Platform terintegrasi untuk manajemen rumah sakit dan fasilitas kesehatan.',
            'address' => 'Jl. Sehat No. 123, Jakarta',
            'phone' => '+62 21 1234567',
            'email' => 'info@webkhanza.com',
            'website' => 'https://webkhanza.com',
            'facebook' => 'https://facebook.com/webkhanza',
            'twitter' => 'https://twitter.com/webkhanza',
            'instagram' => 'https://instagram.com/webkhanza',
            'linkedin' => 'https://linkedin.com/company/webkhanza',
            'youtube' => 'https://youtube.com/@webkhanza',
            'primary_color' => '#2563eb',
            'secondary_color' => '#1e40af',
            'accent_color' => '#dc2626',
            'meta_title' => 'WebKhanza - Sistem Informasi Kesehatan',
            'meta_description' => 'Platform terintegrasi untuk manajemen rumah sakit dan fasilitas kesehatan dengan teknologi modern.',
            'meta_keywords' => json_encode(['webkhanza', 'sistem informasi', 'kesehatan', 'rumah sakit', 'manajemen']),
        ]);

        $this->command->info('Website identity seeded successfully!');
    }
}