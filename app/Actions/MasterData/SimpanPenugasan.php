<?php

namespace App\Actions\MasterData;

use App\Enums\KodePeran;
use App\Models\Penugasan;
use App\Models\Peran;

class SimpanPenugasan
{
    /**
     * Simpan penugasan; kolom konteks yang tidak relevan dengan peran dikosongkan.
     *
     * @param  array{id_tahun_ajaran: int|string, id_tenaga_pendidik: int|string, kode_peran: string, id_rombel?: int|string, id_konsentrasi_keahlian?: int|string, id_satuan_pendidikan?: int|string, bidang?: string}  $data
     */
    public function handle(Penugasan $penugasan, array $data): Penugasan
    {
        $penugasan->fill([
            'id_tahun_ajaran' => $data['id_tahun_ajaran'],
            'id_tenaga_pendidik' => $data['id_tenaga_pendidik'],
            'id_peran' => Peran::query()->where('kode', KodePeran::from($data['kode_peran']))->value('id_peran'),
            'id_rombel' => $data['id_rombel'] ?? null,
            'id_konsentrasi_keahlian' => $data['id_konsentrasi_keahlian'] ?? null,
            'id_satuan_pendidikan' => $data['id_satuan_pendidikan'] ?? null,
            'bidang' => $data['bidang'] ?? null,
        ])->save();

        return $penugasan;
    }
}
