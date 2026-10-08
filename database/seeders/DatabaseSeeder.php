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

        // Akun lokal: aryajaya.alamsyah / 12341234
        Pengguna::factory()->denganPeran(KodePeran::Administrator)->create([
            'nama_pengguna' => 'aryajaya.alamsyah',
            'email' => 'aryajaya.alamsyah@gmail.com',
            'password' => '12341234',
        ]);
    }
}
