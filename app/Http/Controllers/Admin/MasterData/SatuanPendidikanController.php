<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Enums\BentukPendidikan;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\SatuanPendidikanRequest;
use App\Models\SatuanPendidikan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SatuanPendidikanController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', SatuanPendidikan::class);

        return view('admin.master-data.satuan-pendidikan.index', [
            'daftarSatuanPendidikan' => SatuanPendidikan::query()
                ->orderBy('nama')
                ->orderBy('id_satuan_pendidikan')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', SatuanPendidikan::class);

        return view('admin.master-data.satuan-pendidikan.form', [
            'satuanPendidikan' => new SatuanPendidikan,
            'pilihanBentukPendidikan' => BentukPendidikan::cases(),
        ]);
    }

    public function store(SatuanPendidikanRequest $request): RedirectResponse
    {
        SatuanPendidikan::create($request->validated());

        return redirect()->route('admin.master-data.satuan-pendidikan.index')
            ->with('status', 'Satuan pendidikan berhasil ditambahkan.');
    }

    public function edit(SatuanPendidikan $satuanPendidikan): View
    {
        Gate::authorize('update', $satuanPendidikan);

        return view('admin.master-data.satuan-pendidikan.form', [
            'satuanPendidikan' => $satuanPendidikan,
            'pilihanBentukPendidikan' => BentukPendidikan::cases(),
        ]);
    }

    public function update(SatuanPendidikanRequest $request, SatuanPendidikan $satuanPendidikan): RedirectResponse
    {
        $satuanPendidikan->update($request->validated());

        return redirect()->route('admin.master-data.satuan-pendidikan.index')
            ->with('status', 'Satuan pendidikan berhasil diperbarui.');
    }

    public function destroy(SatuanPendidikan $satuanPendidikan): RedirectResponse
    {
        Gate::authorize('delete', $satuanPendidikan);

        $satuanPendidikan->delete();

        return redirect()->route('admin.master-data.satuan-pendidikan.index')
            ->with('status', 'Satuan pendidikan berhasil dihapus.');
    }
}
