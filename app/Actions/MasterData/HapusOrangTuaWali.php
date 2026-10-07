<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\OrangTuaWali;

class HapusOrangTuaWali
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(OrangTuaWali $orangTuaWali): void
    {
        if ($orangTuaWali->murid()->exists()) {
            throw new DataMasihDipakai("{$orangTuaWali->nama} masih tertaut ke murid. Lepaskan tautannya terlebih dahulu.");
        }

        $orangTuaWali->delete();
    }
}
