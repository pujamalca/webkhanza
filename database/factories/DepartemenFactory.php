<?php

namespace Database\Factories;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartemenFactory extends Factory
{
    protected $model = Departemen::class;

    public function definition(): array
    {
        return [
            'dep_id' => $this->faker->unique()->regexify('[A-Z]{4}'),
            'nama' => $this->faker->randomElement([
                'Departemen Medis',
                'Departemen Penunjang',
                'Departemen Administrasi',
                'Departemen Keuangan',
                'Departemen IT',
                'Departemen Farmasi'
            ]),
        ];
    }
}