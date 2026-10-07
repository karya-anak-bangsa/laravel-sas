<?php

namespace App\Actions\MasterData;

use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AktifkanSemester
{
    /**
     * Jadikan semester ini satu-satunya semester aktif.
     * Tahun ajaran aktif adalah tahun ajaran dari semester aktif.
     */
    public function handle(Semester $semester): void
    {
        DB::transaction(function () use ($semester) {
            Semester::query()
                ->where('aktif', true)
                ->whereKeyNot($semester->getKey())
                ->lockForUpdate()
                ->update(['aktif' => false]);

            $semester->update(['aktif' => true]);
        });
    }
}
