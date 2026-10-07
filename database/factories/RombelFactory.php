<?php

namespace Database\Factories;

use App\Enums\Tingkat;
use App\Models\KonsentrasiKeahlian;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rombel>
 */
class RombelFactory extends Factory
{
    /**
     * Rombel SMP secara bawaan.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_tahun_ajaran' => TahunAjaran::factory(),
            'id_satuan_pendidikan' => SatuanPendidikan::factory()->smp(),
            'id_konsentrasi_keahlian' => null,
            'tingkat' => Tingkat::VII,
            'nama' => 'VII '.fake()->unique()->bothify('?#'),
        ];
    }

    /**
     * Rombel SMK dengan konsentrasi keahlian.
     */
    public function smk(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_satuan_pendidikan' => SatuanPendidikan::factory()->smk(),
            'id_konsentrasi_keahlian' => KonsentrasiKeahlian::factory(),
            'tingkat' => Tingkat::X,
            'nama' => 'X '.fake()->unique()->bothify('?#'),
        ]);
    }
}
