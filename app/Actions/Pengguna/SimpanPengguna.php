<?php

namespace App\Actions\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\TenagaPendidik;
use Illuminate\Support\Facades\DB;

class SimpanPengguna
{
    /**
     * Simpan akun, peran tetapnya, dan tautan ke data tenaga pendidik dalam satu transaksi.
     *
     * @param  array{nama_pengguna: string, email?: string|null, password?: string|null, aktif: bool, peran: list<string>, id_tenaga_pendidik?: int|string|null}  $data
     */
    public function handle(Pengguna $pengguna, array $data): Pengguna
    {
        return DB::transaction(function () use ($pengguna, $data) {
            $pengguna->fill([
                'nama_pengguna' => $data['nama_pengguna'],
                'email' => $data['email'] ?? null,
                'aktif' => $data['aktif'],
            ]);

            // Kata sandi hanya diganti jika diisi.
            if (filled($data['password'] ?? null)) {
                $pengguna->password = $data['password'];
            }

            $pengguna->save();

            $kodePeranTetap = collect($data['peran'])
                ->map(fn (string $kode) => KodePeran::from($kode))
                ->filter(fn (KodePeran $kode) => ! $kode->kontekstual());

            $pengguna->peran()->sync(
                Peran::query()->whereIn('kode', $kodePeranTetap->all())->pluck('id_peran')
            );

            $this->tautkanTenagaPendidik($pengguna, $data['id_tenaga_pendidik'] ?? null);

            return $pengguna;
        });
    }

    private function tautkanTenagaPendidik(Pengguna $pengguna, int|string|null $idTenagaPendidik): void
    {
        TenagaPendidik::query()
            ->where('id_pengguna', $pengguna->id_pengguna)
            ->when($idTenagaPendidik, fn ($query) => $query->whereKeyNot($idTenagaPendidik))
            ->update(['id_pengguna' => null]);

        if ($idTenagaPendidik) {
            TenagaPendidik::query()
                ->whereKey($idTenagaPendidik)
                ->update(['id_pengguna' => $pengguna->id_pengguna]);
        }
    }
}
