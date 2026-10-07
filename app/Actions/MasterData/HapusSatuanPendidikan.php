<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\SatuanPendidikan;

class HapusSatuanPendidikan
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(SatuanPendidikan $satuanPendidikan): void
    {
        if ($satuanPendidikan->rombel()->exists()) {
            throw new DataMasihDipakai("Satuan pendidikan {$satuanPendidikan->nama} masih memiliki rombel.");
        }

        $satuanPendidikan->delete();
    }
}
