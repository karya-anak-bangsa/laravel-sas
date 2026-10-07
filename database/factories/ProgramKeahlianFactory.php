<?php

namespace Database\Factories;

use App\Models\BidangKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramKeahlian>
 */
class ProgramKeahlianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_bidang_keahlian' => BidangKeahlian::factory(),
            'nama' => 'Program '.fake()->unique()->words(2, true),
        ];
    }
}
