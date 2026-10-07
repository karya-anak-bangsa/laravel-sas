<?php

namespace Database\Factories;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\ModaTransportasi;
use App\Enums\TempatTinggal;
use App\Models\Murid;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Murid>
 */
class MuridFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(JenisKelamin::cases()),
            'nisn' => fake()->unique()->numerify('##########'),
            'nik' => fake()->unique()->numerify('################'),
            'no_kk' => fake()->numerify('################'),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d'),
            'agama' => Agama::Islam,
            'alamat_jalan' => fake()->streetAddress(),
            'rt' => fake()->numerify('00#'),
            'rw' => fake()->numerify('00#'),
            'kelurahan_desa' => fake()->citySuffix(),
            'kecamatan' => fake()->city(),
            'kode_pos' => fake()->numerify('15###'),
            'moda_transportasi' => ModaTransportasi::KendaraanUmum,
            'tempat_tinggal' => TempatTinggal::BersamaOrangTua,
            'nomor_hp' => fake()->numerify('08##########'),
        ];
    }
}
