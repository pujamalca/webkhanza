<?php

namespace Database\Factories;

use App\Models\JnjJabatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class JnjJabatanFactory extends Factory
{
    protected $model = JnjJabatan::class;

    public function definition(): array
    {
        return [
            'kode' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'nama' => $this->faker->randomElement([
                'Dokter Umum',
                'Dokter Spesialis',
                'Perawat',
                'Bidan',
                'Apoteker',
                'Tenaga Administrasi'
            ]),
            'tnj' => $this->faker->numberBetween(500000, 2000000),
            'indek' => $this->faker->numberBetween(1, 4),
        ];
    }
}