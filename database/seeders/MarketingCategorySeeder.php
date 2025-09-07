<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingCategory;

class MarketingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Follow Up Pasien',
                'description' => 'Follow up kondisi pasien setelah pemeriksaan',
                'is_active' => true,
            ],
            [
                'name' => 'Survey Kepuasan',
                'description' => 'Survey tingkat kepuasan terhadap pelayanan',
                'is_active' => true,
            ],
            [
                'name' => 'Reminder Kontrol',
                'description' => 'Mengingatkan pasien untuk kontrol rutin',
                'is_active' => true,
            ],
            [
                'name' => 'Promosi Layanan',
                'description' => 'Memberikan informasi promosi layanan kesehatan',
                'is_active' => true,
            ],
            [
                'name' => 'Feedback Complaint',
                'description' => 'Menangani feedback dan keluhan pasien',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MarketingCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}