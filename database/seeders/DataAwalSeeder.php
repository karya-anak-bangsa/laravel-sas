<?php

namespace Database\Seeders;

use App\Enums\BentukPendidikan;
use App\Models\SatuanPendidikan;
use Illuminate\Database\Seeder;

/**
 * Data awal yayasan yang dibutuhkan di semua lingkungan, termasuk production.
 *
 * Aman dijalankan berulang: `php artisan db:seed --class=DataAwalSeeder`.
 */
class DataAwalSeeder extends Seeder
{
    public function run(): void
    {
        $this->satuanPendidikan();
    }

    private function satuanPendidikan(): void
    {
        SatuanPendidikan::query()->firstOrCreate(
            ['bentuk_pendidikan' => BentukPendidikan::Smp, 'nama' => 'SMP Puspita Bangsa'],
        );

        SatuanPendidikan::query()->firstOrCreate(
            ['bentuk_pendidikan' => BentukPendidikan::Smk, 'nama' => 'SMK Puspita Bangsa'],
        );
    }
}
