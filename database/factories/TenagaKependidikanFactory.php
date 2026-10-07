<?php

namespace Database\Factories;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Models\TenagaKependidikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenagaKependidikan>
 */
class TenagaKependidikanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('################'),
            'nama_lengkap' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(JenisKelamin::cases()),
            'alamat' => fake()->streetAddress(),
            'rt' => fake()->numerify('00#'),
            'rw' => fake()->numerify('00#'),
            'kelurahan_desa' => fake()->citySuffix(),
            'kecamatan' => fake()->city(),
            'agama' => Agama::Islam,
        ];
    }
}
