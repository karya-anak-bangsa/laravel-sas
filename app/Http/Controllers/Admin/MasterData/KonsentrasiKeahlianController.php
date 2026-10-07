<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusKonsentrasiKeahlian;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\KonsentrasiKeahlianRequest;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KonsentrasiKeahlianController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', KonsentrasiKeahlian::class);

        return view('admin.master-data.konsentrasi-keahlian.index', [
            'daftarKonsentrasiKeahlian' => KonsentrasiKeahlian::query()
                ->with('programKeahlian.bidangKeahlian')
                ->orderBy('nama')
                ->orderBy('id_konsentrasi_keahlian')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', KonsentrasiKeahlian::class);

        return $this->form(new KonsentrasiKeahlian);
    }

    public function store(KonsentrasiKeahlianRequest $request): RedirectResponse
    {
        KonsentrasiKeahlian::create($request->validated());

        return redirect()->route('admin.master-data.konsentrasi-keahlian.index')
            ->with('status', 'Konsentrasi keahlian berhasil ditambahkan.');
    }

    public function edit(KonsentrasiKeahlian $konsentrasiKeahlian): View
    {
        Gate::authorize('update', $konsentrasiKeahlian);

        return $this->form($konsentrasiKeahlian);
    }

    public function update(KonsentrasiKeahlianRequest $request, KonsentrasiKeahlian $konsentrasiKeahlian): RedirectResponse
    {
        $konsentrasiKeahlian->update($request->validated());

        return redirect()->route('admin.master-data.konsentrasi-keahlian.index')
            ->with('status', 'Konsentrasi keahlian berhasil diperbarui.');
    }

    public function destroy(KonsentrasiKeahlian $konsentrasiKeahlian, HapusKonsentrasiKeahlian $hapus): RedirectResponse
    {
        Gate::authorize('delete', $konsentrasiKeahlian);

        $hapus->handle($konsentrasiKeahlian);

        return redirect()->route('admin.master-data.konsentrasi-keahlian.index')
            ->with('status', 'Konsentrasi keahlian berhasil dihapus.');
    }

    private function form(KonsentrasiKeahlian $konsentrasiKeahlian): View
    {
        return view('admin.master-data.konsentrasi-keahlian.form', [
            'konsentrasiKeahlian' => $konsentrasiKeahlian,
            'pilihanProgramKeahlian' => ProgramKeahlian::query()->orderBy('nama')->pluck('nama', 'id_program_keahlian'),
        ]);
    }
}
