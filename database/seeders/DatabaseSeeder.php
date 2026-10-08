<?php

namespace Database\Seeders;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Data awal yayasan selalu diisi. Data contoh hanya untuk pengembangan
     * lokal; di production, buat akun dengan `php artisan pengguna:buat-administrator`.
     */
    public function run(): void
    {
        $this->call(DataAwalSeeder::class);

        if (app()->isProduction()) {
            $this->command->warn('Seeder data contoh tidak dijalankan di production.');

            return;
        }

        // Akun lokal: aryajayaalamsyah@gmail.com / 12341234
        Pengguna::factory()->denganPeran(KodePeran::Administrator)->create([
            'email' => 'aryajayaalamsyah@gmail.com',
            'password' => '12341234',
        ]);

        // Master data contoh tanpa akun pengguna lain.
        $this->call(DataContohSeeder::class);
    }
}
