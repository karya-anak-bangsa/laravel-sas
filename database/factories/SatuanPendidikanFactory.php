<?php

namespace Database\Factories;

use App\Enums\BentukPendidikan;
use App\Models\SatuanPendidikan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SatuanPendidikan>
 */
class SatuanPendidikanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'SMP '.fake()->unique()->company(),
            'bentuk_pendidikan' => BentukPendidikan::Smp,
            'npsn' => fake()->unique()->numerify('########'),
            'alamat' => fake()->address(),
        ];
    }

    public function smp(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'SMP '.fake()->unique()->company(),
            'bentuk_pendidikan' => BentukPendidikan::Smp,
        ]);
    }

    public function smk(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'SMK '.fake()->unique()->company(),
            'bentuk_pendidikan' => BentukPendidikan::Smk,
        ]);
    }
}
