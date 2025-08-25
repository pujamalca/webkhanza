<?php

namespace Database\Factories;

use App\Models\Bidang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidangFactory extends Factory
{
    protected $model = Bidang::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement([
                'Medis',
                'Keperawatan',
                'Farmasi',
                'Laboratorium',
                'Radiologi',
                'Administrasi',
                'Keuangan',
                'IT',
                'Humas',
                'Logistik'
            ]),
        ];
    }
}