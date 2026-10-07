<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\TahunAjaran;

class HapusTahunAjaran
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(TahunAjaran $tahunAjaran): void
    {
        if ($tahunAjaran->semester()->where('aktif', true)->exists()) {
            throw new DataMasihDipakai("Tahun ajaran {$tahunAjaran->nama} sedang aktif dan tidak dapat dihapus.");
        }

        if ($tahunAjaran->rombel()->exists()) {
            throw new DataMasihDipakai("Tahun ajaran {$tahunAjaran->nama} masih memiliki rombel.");
        }

        $tahunAjaran->delete();
    }
}
