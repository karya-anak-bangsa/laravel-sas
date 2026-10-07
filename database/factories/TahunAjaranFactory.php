<?php

namespace Database\Factories;

use App\Enums\JenisSemester;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahun = fake()->unique()->numberBetween(2000, 2099);

        return [
            'nama' => $tahun.'/'.($tahun + 1),
        ];
    }

    /**
     * Buat semester ganjil (Juli–Desember) dan genap (Januari–Juni).
     */
    public function denganSemester(?JenisSemester $aktif = null): static
    {
        return $this->afterCreating(function (TahunAjaran $tahunAjaran) use ($aktif) {
            $tahun = (int) substr($tahunAjaran->nama, 0, 4);

            Semester::factory()->for($tahunAjaran)->create([
                'jenis' => JenisSemester::Ganjil,
                'tanggal_mulai' => "{$tahun}-07-01",
                'tanggal_selesai' => "{$tahun}-12-31",
                'aktif' => $aktif === JenisSemester::Ganjil,
            ]);

            Semester::factory()->for($tahunAjaran)->create([
                'jenis' => JenisSemester::Genap,
                'tanggal_mulai' => ($tahun + 1).'-01-01',
                'tanggal_selesai' => ($tahun + 1).'-06-30',
                'aktif' => $aktif === JenisSemester::Genap,
            ]);
        });
    }
}
