<?php

namespace App\Actions\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\TenagaKependidikan;
use App\Models\TenagaPendidik;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SimpanPengguna
{
    /**
     * Simpan akun, peran tetapnya, dan tautan ke data tenaga pendidik/kependidikan
     * dalam satu transaksi.
     *
     * @param  array{email: string, password?: string|null, aktif: bool, peran: list<string>, id_tenaga_pendidik?: int|string|null, id_tenaga_kependidikan?: int|string|null}  $data
     */
    public function handle(Pengguna $pengguna, array $data): Pengguna
    {
        return DB::transaction(function () use ($pengguna, $data) {
            $pengguna->fill([
                'email' => $data['email'],
                'aktif' => $data['aktif'],
            ]);

            // Password hanya diganti jika diisi.
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

            $this->tautkan(TenagaPendidik::class, $pengguna, $data['id_tenaga_pendidik'] ?? null);
            $this->tautkan(TenagaKependidikan::class, $pengguna, $data['id_tenaga_kependidikan'] ?? null);

            return $pengguna;
        });
    }

    /**
     * Tautkan akun ke satu baris data pribadi dan lepaskan tautan lamanya.
     *
     * @param  class-string<Model>  $kelasModel
     */
    private function tautkan(string $kelasModel, Pengguna $pengguna, int|string|null $id): void
    {
        $kelasModel::query()
            ->where('id_pengguna', $pengguna->id_pengguna)
            ->when($id, fn ($query) => $query->whereKeyNot($id))
            ->update(['id_pengguna' => null]);

        if ($id) {
            $kelasModel::query()->whereKey($id)->update(['id_pengguna' => $pengguna->id_pengguna]);
        }
    }
}
