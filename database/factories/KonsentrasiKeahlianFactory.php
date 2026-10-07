<?php

namespace Database\Factories;

use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KonsentrasiKeahlian>
 */
class KonsentrasiKeahlianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_program_keahlian' => ProgramKeahlian::factory(),
            'nama' => 'Konsentrasi '.fake()->unique()->words(2, true),
            'singkatan' => fake()->unique()->lexify('???'),
        ];
    }
}
