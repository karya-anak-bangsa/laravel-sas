<?php

namespace Database\Factories;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pengguna' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'aktif' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Berikan peran kepada pengguna setelah dibuat.
     */
    public function denganPeran(KodePeran ...$kode): static
    {
        return $this->afterCreating(function (Pengguna $pengguna) use ($kode) {
            $pengguna->peran()->attach(
                Peran::query()->whereIn('kode', $kode)->pluck('id_peran')
            );
        });
    }

    /**
     * Akun dinonaktifkan sehingga tidak dapat masuk.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'aktif' => false,
        ]);
    }
}
