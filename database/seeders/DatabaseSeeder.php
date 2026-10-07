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
     */
    public function run(): void
    {
        Pengguna::factory()->denganPeran(KodePeran::Administrator)->create([
            'nama_pengguna' => 'admin',
            'email' => 'admin@example.com',
        ]);
    }
}
