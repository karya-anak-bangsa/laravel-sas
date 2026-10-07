<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\BidangKeahlian;

class HapusBidangKeahlian
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(BidangKeahlian $bidangKeahlian): void
    {
        if ($bidangKeahlian->programKeahlian()->exists()) {
            throw new DataMasihDipakai("Bidang keahlian {$bidangKeahlian->nama} masih memiliki program keahlian.");
        }

        $bidangKeahlian->delete();
    }
}
