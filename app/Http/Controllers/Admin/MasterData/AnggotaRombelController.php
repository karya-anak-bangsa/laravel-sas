<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\AnggotaRombelRequest;
use App\Models\Murid;
use App\Models\Rombel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AnggotaRombelController extends Controller
{
    public function index(Rombel $rombel): View
    {
        Gate::authorize('view', $rombel);

        $rombel->load(['tahunAjaran', 'satuanPendidikan', 'konsentrasiKeahlian']);

        return view('admin.master-data.rombel.anggota', [
            'rombel' => $rombel,
            'daftarAnggota' => $rombel->murid()->orderBy('nama_lengkap')->paginate(50),
            // Kandidat: murid yang belum memiliki rombel pada tahun ajaran ini.
            'pilihanMurid' => Murid::query()
                ->whereDoesntHave('rombel', fn (Builder $query) => $query->where('id_tahun_ajaran', $rombel->id_tahun_ajaran))
                ->orderBy('nama_lengkap')
                ->get(['id_murid', 'nama_lengkap', 'nisn'])
                ->mapWithKeys(fn (Murid $murid) => [
                    $murid->id_murid => $murid->nama_lengkap.($murid->nisn ? " ({$murid->nisn})" : ''),
                ]),
        ]);
    }

    public function store(AnggotaRombelRequest $request, Rombel $rombel): RedirectResponse
    {
        $ids = $request->collect('id_murid')->map(fn ($id) => (int) $id);

        $rombel->murid()->syncWithoutDetaching($ids->all());

        return redirect()->route('admin.master-data.rombel.anggota.index', $rombel)
            ->with('status', "{$ids->count()} murid ditambahkan ke rombel {$rombel->nama}.");
    }

    public function destroy(Rombel $rombel, Murid $murid): RedirectResponse
    {
        Gate::authorize('update', $rombel);

        $rombel->murid()->detach($murid->id_murid);

        return redirect()->route('admin.master-data.rombel.anggota.index', $rombel)
            ->with('status', "{$murid->nama_lengkap} dikeluarkan dari rombel {$rombel->nama}.");
    }
}
