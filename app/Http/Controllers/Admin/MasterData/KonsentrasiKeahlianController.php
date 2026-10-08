<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusKonsentrasiKeahlian;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\KonsentrasiKeahlianRequest;
use App\Models\BidangKeahlian;
use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;
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
                ->withCount(['rombel' => fn (Builder $query) => $query
                    ->whereIn('id_tahun_ajaran', Semester::query()->aktif()->select('id_tahun_ajaran'))])
                // Urut mengikuti hierarki: bidang keahlian, program keahlian, lalu konsentrasi keahlian.
                ->orderBy(BidangKeahlian::query()
                    ->select('tb_bidang_keahlian.nama')
                    ->join('tb_program_keahlian', 'tb_program_keahlian.id_bidang_keahlian', '=', 'tb_bidang_keahlian.id_bidang_keahlian')
                    ->whereColumn('tb_program_keahlian.id_program_keahlian', 'tb_konsentrasi_keahlian.id_program_keahlian'))
                ->orderBy(ProgramKeahlian::query()
                    ->select('nama')
                    ->whereColumn('tb_program_keahlian.id_program_keahlian', 'tb_konsentrasi_keahlian.id_program_keahlian'))
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
