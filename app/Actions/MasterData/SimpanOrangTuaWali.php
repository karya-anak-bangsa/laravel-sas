<?php

namespace App\Actions\MasterData;

use App\Enums\HubunganOrangTua;
use App\Models\OrangTuaWali;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SimpanOrangTuaWali
{
    /**
     * Simpan data orang tua/wali; jika id_murid diisi, langsung tautkan ke murid tersebut.
     *
     * @param  array{nama: string, pendidikan?: string|null, pekerjaan?: string|null, penghasilan?: string|null, nomor_hp?: string|null, id_murid?: int|string|null, hubungan?: string}  $data
     */
    public function handle(OrangTuaWali $orangTuaWali, array $data): OrangTuaWali
    {
        return DB::transaction(function () use ($orangTuaWali, $data) {
            $orangTuaWali->fill(Arr::except($data, ['id_murid', 'hubungan']))->save();

            if (! empty($data['id_murid'])) {
                $orangTuaWali->murid()->attach($data['id_murid'], [
                    'hubungan' => HubunganOrangTua::from($data['hubungan'])->value,
                ]);
            }

            return $orangTuaWali;
        });
    }
}
