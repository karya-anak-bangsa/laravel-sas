<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusProgramKeahlian;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\ProgramKeahlianRequest;
use App\Models\BidangKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProgramKeahlianController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', ProgramKeahlian::class);

        return view('admin.master-data.program-keahlian.index', [
            'daftarProgramKeahlian' => ProgramKeahlian::query()
                ->with('bidangKeahlian')
                ->withCount('konsentrasiKeahlian')
                ->orderBy('nama')
                ->orderBy('id_program_keahlian')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', ProgramKeahlian::class);

        return $this->form(new ProgramKeahlian);
    }

    public function store(ProgramKeahlianRequest $request): RedirectResponse
    {
        ProgramKeahlian::create($request->validated());

        return redirect()->route('admin.master-data.program-keahlian.index')
            ->with('status', 'Program keahlian berhasil ditambahkan.');
    }

    public function edit(ProgramKeahlian $programKeahlian): View
    {
        Gate::authorize('update', $programKeahlian);

        return $this->form($programKeahlian);
    }

    public function update(ProgramKeahlianRequest $request, ProgramKeahlian $programKeahlian): RedirectResponse
    {
        $programKeahlian->update($request->validated());

        return redirect()->route('admin.master-data.program-keahlian.index')
            ->with('status', 'Program keahlian berhasil diperbarui.');
    }

    public function destroy(ProgramKeahlian $programKeahlian, HapusProgramKeahlian $hapus): RedirectResponse
    {
        Gate::authorize('delete', $programKeahlian);

        $hapus->handle($programKeahlian);

        return redirect()->route('admin.master-data.program-keahlian.index')
            ->with('status', 'Program keahlian berhasil dihapus.');
    }

    private function form(ProgramKeahlian $programKeahlian): View
    {
        return view('admin.master-data.program-keahlian.form', [
            'programKeahlian' => $programKeahlian,
            'pilihanBidangKeahlian' => BidangKeahlian::query()->orderBy('nama')->pluck('nama', 'id_bidang_keahlian'),
        ]);
    }
}
