<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\MuridOrangTuaRequest;
use App\Models\Murid;
use App\Models\OrangTuaWali;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Tautan murid ↔ orang tua/wali (dikelola dari halaman ubah murid).
 */
class MuridOrangTuaController extends Controller
{
    public function store(MuridOrangTuaRequest $request, Murid $murid): RedirectResponse
    {
        $murid->orangTuaWali()->attach($request->integer('id_orang_tua_wali'), [
            'hubungan' => $request->string('hubungan')->value(),
        ]);

        return redirect()->route('admin.master-data.murid.edit', $murid)
            ->with('status', 'Orang tua/wali berhasil ditautkan.');
    }

    public function destroy(Murid $murid, OrangTuaWali $orangTuaWali): RedirectResponse
    {
        Gate::authorize('update', $murid);

        $murid->orangTuaWali()->detach($orangTuaWali->id_orang_tua_wali);

        return redirect()->route('admin.master-data.murid.edit', $murid)
            ->with('status', 'Tautan orang tua/wali berhasil dilepas.');
    }
}
