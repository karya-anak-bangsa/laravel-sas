<?php

namespace App\Actions\MasterData;

use App\Exceptions\DataMasihDipakai;
use App\Models\TenagaPendidik;

class HapusTenagaPendidik
{
    /**
     * @throws DataMasihDipakai
     */
    public function handle(TenagaPendidik $tenagaPendidik): void
    {
        if ($tenagaPendidik->penugasan()->exists()) {
            throw new DataMasihDipakai("{$tenagaPendidik->nama_lengkap} masih memiliki penugasan. Hapus penugasannya terlebih dahulu.");
        }

        $tenagaPendidik->delete();
    }
}
