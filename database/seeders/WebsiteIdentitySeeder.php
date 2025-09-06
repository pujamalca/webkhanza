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
            'tagline' => 'Sistem Informasi Kesehatan Terpadu',
            'description' => 'Platform terintegrasi untuk manajemen rumah sakit, klinik, dan fasilitas kesehatan dengan teknologi modern dan user-friendly interface.',
            'address' => 'Jl. Kesehatan Raya No. 123, Jakarta Pusat, DKI Jakarta 10110, Indonesia',
            'phone' => '+62 21 8012-3456',
            'email' => 'info@webkhanza.com',
            'social_media' => json_encode([
                'website' => 'https://webkhanza.com',
                'facebook' => 'https://facebook.com/webkhanza',
                'twitter' => 'https://twitter.com/webkhanza',
                'instagram' => 'https://instagram.com/webkhanza',
                'linkedin' => 'https://linkedin.com/company/webkhanza',
                'youtube' => 'https://youtube.com/@webkhanza',
                'whatsapp' => '+62 812-3456-7890',
                'telegram' => 'https://t.me/webkhanza'
            ]),
            'colors' => json_encode([
                'primary' => '#2563eb',
                'secondary' => '#1e40af',
                'accent' => '#dc2626',
                'success' => '#059669',
                'warning' => '#d97706',
                'danger' => '#dc2626',
                'info' => '#0284c7'
            ]),
            'primary_color' => '#2563eb',
            'secondary_color' => '#1e40af',
            'accent_color' => '#dc2626',
            'landing_template' => 'default'
        ]);

        $this->command->info('Website identity seeded successfully!');
    }
}