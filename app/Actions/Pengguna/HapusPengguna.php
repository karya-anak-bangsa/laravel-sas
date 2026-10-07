<?php

namespace App\Actions\Pengguna;

use App\Exceptions\DataMasihDipakai;
use App\Models\Pengguna;
use App\Models\TenagaKependidikan;
use App\Models\TenagaPendidik;
use Illuminate\Support\Facades\DB;

class HapusPengguna
{
    /**
     * Hapus akun (soft delete) dan lepaskan tautannya dari data tenaga pendidik/kependidikan.
     *
     * @throws DataMasihDipakai
     */
    public function handle(Pengguna $pengguna, Pengguna $pelaku): void
    {
        if ($pengguna->is($pelaku)) {
            throw new DataMasihDipakai('Akun yang sedang Anda pakai tidak dapat dihapus.');
        }

        DB::transaction(function () use ($pengguna) {
            TenagaPendidik::query()->where('id_pengguna', $pengguna->id_pengguna)->update(['id_pengguna' => null]);
            TenagaKependidikan::query()->where('id_pengguna', $pengguna->id_pengguna)->update(['id_pengguna' => null]);

            $pengguna->delete();
        });
    }
}
