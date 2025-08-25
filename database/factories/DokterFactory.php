<?php

namespace Database\Factories;

use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    protected $model = Dokter::class;

    public function definition(): array
    {
        return [
            'kd_dokter' => $this->faker->unique()->numerify('################'),
            'nm_dokter' => 'Dr. ' . $this->faker->name(),
            'jk' => $this->faker->randomElement(['L', 'P']),
            'tmp_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '1980-01-01'),
            'gol_drh' => $this->faker->randomElement(['A', 'B', 'O', 'AB', '-']),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Hindu', 'Buddha']),
            'almt_tgl' => $this->faker->address(),
            'no_telp' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'stts_nikah' => $this->faker->randomElement(['BELUM MENIKAH', 'MENIKAH', 'JANDA', 'DUDHA', 'JOMBLO']),
            'kd_sps' => $this->faker->optional()->randomElement(['SP001', 'SP002', 'SP003', null]),
            'alumni' => $this->faker->optional()->company(),
            'no_ijn_praktek' => $this->faker->optional()->regexify('[0-9]{10}'),
            'status' => $this->faker->boolean(),
        ];
    }
}