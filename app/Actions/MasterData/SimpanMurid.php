<?php

namespace App\Actions\MasterData;

use App\Models\Murid;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SimpanMurid
{
    /**
     * Simpan identitas murid beserta daftar kebutuhan khususnya.
     *
     * @param  array<string, mixed>  $data  Data tervalidasi dari MuridRequest.
     */
    public function handle(Murid $murid, array $data): Murid
    {
        return DB::transaction(function () use ($murid, $data) {
            $murid->fill(Arr::except($data, ['berkebutuhan_khusus']))->save();

            $kodeBaru = collect($data['berkebutuhan_khusus'] ?? [])->unique()->values();

            $murid->berkebutuhanKhusus()->whereNotIn('kode', $kodeBaru->all())->delete();

            foreach ($kodeBaru as $kode) {
                $murid->berkebutuhanKhusus()->firstOrCreate(['kode' => $kode]);
            }

            return $murid;
        });
    }
}
