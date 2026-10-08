<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\Rombel;

class HapusRombel
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(Rombel $rombel): void
    {
        if ($rombel->murid()->exists()) {
            throw new DataMasihDipakai("Rombel {$rombel->nama} masih memiliki anggota. Keluarkan muridnya terlebih dahulu.");
        }

        if ($rombel->penugasan()->exists()) {
            throw new DataMasihDipakai("Rombel {$rombel->nama} masih memiliki wali kelas. Hapus penugasannya terlebih dahulu.");
        }

        $rombel->delete();
    }
}
