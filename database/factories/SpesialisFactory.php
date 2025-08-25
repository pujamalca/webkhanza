<?php

namespace Database\Factories;

use App\Models\Spesialis;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpesialisFactory extends Factory
{
    protected $model = Spesialis::class;

    public function definition(): array
    {
        return [
            'kd_sps' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'nm_sps' => $this->faker->randomElement([
                'Spesialis Anak',
                'Spesialis Penyakit Dalam',
                'Spesialis Bedah',
                'Spesialis Obstetri dan Ginekologi',
                'Spesialis Jantung',
                'Spesialis Mata',
                'Spesialis THT',
                'Spesialis Kulit dan Kelamin',
                'Spesialis Saraf',
                'Spesialis Jiwa',
                'Spesialis Radiologi',
                'Spesialis Anestesi',
                'Spesialis Patologi Klinik',
                'Spesialis Rehabilitasi Medik'
            ]),
        ];
    }
}