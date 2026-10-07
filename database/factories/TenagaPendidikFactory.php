<?php

namespace Database\Factories;

use App\Enums\JenjangPendidikan;
use App\Enums\StatusTenagaPendidik;
use App\Models\SatuanPendidikan;
use App\Models\TenagaPendidik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenagaPendidik>
 */
class TenagaPendidikFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_satuan_pendidikan' => SatuanPendidikan::factory(),
            'nama_lengkap' => fake()->name(),
            'nuptk' => fake()->unique()->numerify('################'),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-55 years', '-23 years')->format('Y-m-d'),
            'pendidikan_terakhir' => JenjangPendidikan::S1,
            'status' => StatusTenagaPendidik::Gtt,
            'tmt_gtt' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            'tmt_gty' => null,
            'masa_kerja_tahun' => fake()->numberBetween(0, 20),
            'masa_kerja_bulan' => fake()->numberBetween(0, 11),
        ];
    }

    public function gty(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTenagaPendidik::Gty,
            'tmt_gty' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
        ]);
    }
}
