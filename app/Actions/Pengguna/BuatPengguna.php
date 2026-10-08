<?php

namespace App\Actions\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Support\Facades\DB;

class BuatPengguna
{
    /**
     * Buat akun pengguna beserta perannya dalam satu transaksi.
     */
    public function handle(string $email, string $password, KodePeran ...$peran): Pengguna
    {
        return DB::transaction(function () use ($email, $password, $peran) {
            $pengguna = Pengguna::create([
                'email' => $email,
                'password' => $password,
                'aktif' => true,
            ]);

            $pengguna->peran()->attach(
                Peran::query()->whereIn('kode', $peran)->pluck('id_peran')
            );

            return $pengguna;
        });
    }
}
