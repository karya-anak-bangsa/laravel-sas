<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusTahunAjaran;
use App\Actions\MasterData\SimpanTahunAjaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\TahunAjaranRequest;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', TahunAjaran::class);

        return view('admin.master-data.tahun-ajaran.index', [
            'daftarTahunAjaran' => TahunAjaran::query()
                ->with(['semesterGanjil', 'semesterGenap'])
                ->orderByDesc('nama')
                ->orderByDesc('id_tahun_ajaran')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', TahunAjaran::class);

        return view('admin.master-data.tahun-ajaran.form', [
            'tahunAjaran' => new TahunAjaran,
        ]);
    }

    public function store(TahunAjaranRequest $request, SimpanTahunAjaran $simpan): RedirectResponse
    {
        $simpan->handle(new TahunAjaran, $request->validated());

        return redirect()->route('admin.master-data.tahun-ajaran.index')
            ->with('status', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran): View
    {
        Gate::authorize('update', $tahunAjaran);

        return view('admin.master-data.tahun-ajaran.form', [
            'tahunAjaran' => $tahunAjaran->load(['semesterGanjil', 'semesterGenap']),
        ]);
    }

    public function update(TahunAjaranRequest $request, TahunAjaran $tahunAjaran, SimpanTahunAjaran $simpan): RedirectResponse
    {
        $simpan->handle($tahunAjaran, $request->validated());

        return redirect()->route('admin.master-data.tahun-ajaran.index')
            ->with('status', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran, HapusTahunAjaran $hapus): RedirectResponse
    {
        Gate::authorize('delete', $tahunAjaran);

        $hapus->handle($tahunAjaran);

        return redirect()->route('admin.master-data.tahun-ajaran.index')
            ->with('status', 'Tahun ajaran berhasil dihapus.');
    }
}
