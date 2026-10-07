<?php

namespace Database\Factories;

use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use App\Models\OrangTuaWali;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrangTuaWali>
 */
class OrangTuaWaliFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'pendidikan' => PendidikanOrangTua::Sma,
            'pekerjaan' => PekerjaanOrangTua::KaryawanSwasta,
            'penghasilan' => PenghasilanOrangTua::Antara2JutaDan5Juta,
            'nomor_hp' => fake()->numerify('08##########'),
        ];
    }
}
