<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\ProgramKeahlian;

class HapusProgramKeahlian
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(ProgramKeahlian $programKeahlian): void
    {
        if ($programKeahlian->konsentrasiKeahlian()->exists()) {
            throw new DataMasihDipakai("Program keahlian {$programKeahlian->nama} masih memiliki konsentrasi keahlian.");
        }

        $programKeahlian->delete();
    }
}
