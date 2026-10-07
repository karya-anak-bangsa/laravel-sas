<?php

namespace App\Actions\MasterData;

use App\Enums\JenisSemester;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class SimpanTahunAjaran
{
    /**
     * Buat atau ubah tahun ajaran beserta kedua semesternya.
     *
     * @param  array{nama: string, ganjil_mulai: string, ganjil_selesai: string, genap_mulai: string, genap_selesai: string}  $data
     */
    public function handle(TahunAjaran $tahunAjaran, array $data): TahunAjaran
    {
        return DB::transaction(function () use ($tahunAjaran, $data) {
            $tahunAjaran->fill(['nama' => $data['nama']])->save();

            foreach (JenisSemester::cases() as $jenis) {
                $tahunAjaran->semester()->updateOrCreate(
                    ['jenis' => $jenis],
                    [
                        'tanggal_mulai' => $data["{$jenis->value}_mulai"],
                        'tanggal_selesai' => $data["{$jenis->value}_selesai"],
                    ],
                );
            }

            return $tahunAjaran;
        });
    }
}
