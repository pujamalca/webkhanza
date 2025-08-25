<?php

namespace Database\Factories;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'jk' => $this->faker->randomElement(['Pria', 'Wanita']),
            'jbtn' => $this->faker->randomElement(['Dokter Umum', 'Perawat', 'Bidan', 'Apoteker']),
            'jnj_jabatan' => 'JJ001',
            'kode_kelompok' => 'KJ1',
            'kode_resiko' => 'R01',
            'kode_emergency' => 'E01',
            'departemen' => 'DEPT',
            'bidang' => 'Medis',
            'stts_wp' => 'TK/0',
            'stts_kerja' => 'PNS',
            'npwp' => $this->faker->optional()->numerify('##.###.###.#-###.###'),
            'pendidikan' => 'S1',
            'gapok' => $this->faker->numberBetween(3000000, 15000000),
            'tmp_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '2000-01-01'),
            'alamat' => $this->faker->address(),
            'kota' => $this->faker->city(),
            'mulai_kerja' => $this->faker->date('Y-m-d', 'now'),
            'ms_kerja' => $this->faker->randomElement(['<1', 'PT', 'FT>1']),
            'indexins' => $this->faker->optional()->regexify('[A-Z]{1}[0-9]{3}'),
            'bpd' => 'Bank BCA',
            'rekening' => $this->faker->optional()->numerify('##########'),
            'stts_aktif' => $this->faker->randomElement(['AKTIF', 'CUTI', 'KELUAR', 'TENAGA LUAR', 'NON AKTIF']),
            'wajibmasuk' => $this->faker->numberBetween(20, 25),
            'pengurang' => $this->faker->numberBetween(0, 500000),
            'indek' => $this->faker->numberBetween(1, 4),
            'mulai_kontrak' => $this->faker->optional()->date('Y-m-d', 'now'),
            'cuti_diambil' => $this->faker->numberBetween(0, 12),
            'dankes' => $this->faker->numberBetween(25000, 100000),
            'photo' => null,
            'no_ktp' => $this->faker->unique()->numerify('################'),
        ];
    }

    /**
     * Indicate that the pegawai is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'stts_aktif' => 'AKTIF',
        ]);
    }

    /**
     * Indicate that the pegawai is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'stts_aktif' => 'NON AKTIF',
        ]);
    }

    /**
     * Indicate that the pegawai is a doctor.
     */
    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'jbtn' => 'Dokter Umum',
            'jnj_jabatan' => 'JJ001',
            'gapok' => $this->faker->numberBetween(8000000, 20000000),
        ]);
    }

    /**
     * Indicate that the pegawai is a nurse.
     */
    public function nurse(): static
    {
        return $this->state(fn (array $attributes) => [
            'jbtn' => 'Perawat',
            'jnj_jabatan' => 'JJ002',
            'gapok' => $this->faker->numberBetween(4000000, 8000000),
        ]);
    }
}