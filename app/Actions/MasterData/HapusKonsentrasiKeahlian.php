<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\KonsentrasiKeahlian;

class HapusKonsentrasiKeahlian
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(KonsentrasiKeahlian $konsentrasiKeahlian): void
    {
        if ($konsentrasiKeahlian->rombel()->exists()) {
            throw new DataMasihDipakai("Konsentrasi keahlian {$konsentrasiKeahlian->nama} masih dipakai oleh rombel.");
        }

        if ($konsentrasiKeahlian->penugasan()->exists()) {
            throw new DataMasihDipakai("Konsentrasi keahlian {$konsentrasiKeahlian->nama} masih dipakai oleh penugasan.");
        }

        $konsentrasiKeahlian->delete();
    }
}
