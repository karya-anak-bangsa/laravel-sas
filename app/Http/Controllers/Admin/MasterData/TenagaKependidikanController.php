<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\TenagaKependidikanRequest;
use App\Models\TenagaKependidikan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenagaKependidikanController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TenagaKependidikan::class);

        $cari = $request->string('cari')->trim()->value();

        return view('admin.master-data.tenaga-kependidikan.index', [
            'daftarTenagaKependidikan' => TenagaKependidikan::query()
                ->with('pengguna')
                ->when($cari !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('nama_lengkap', 'like', "%{$cari}%")
                    ->orWhere('nik', 'like', "{$cari}%")))
                ->orderBy('nama_lengkap')
                ->orderBy('id_tenaga_kependidikan')
                ->paginate(20)
                ->withQueryString(),
            'cari' => $cari,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', TenagaKependidikan::class);

        return $this->form(new TenagaKependidikan);
    }

    public function store(TenagaKependidikanRequest $request): RedirectResponse
    {
        TenagaKependidikan::create($request->validated());

        return redirect()->route('admin.master-data.tenaga-kependidikan.index')
            ->with('status', 'Data tenaga kependidikan berhasil ditambahkan.');
    }

    public function edit(TenagaKependidikan $tenagaKependidikan): View
    {
        Gate::authorize('update', $tenagaKependidikan);

        return $this->form($tenagaKependidikan);
    }

    public function update(TenagaKependidikanRequest $request, TenagaKependidikan $tenagaKependidikan): RedirectResponse
    {
        $tenagaKependidikan->update($request->validated());

        return redirect()->route('admin.master-data.tenaga-kependidikan.index')
            ->with('status', 'Data tenaga kependidikan berhasil diperbarui.');
    }

    public function destroy(TenagaKependidikan $tenagaKependidikan): RedirectResponse
    {
        Gate::authorize('delete', $tenagaKependidikan);

        $tenagaKependidikan->delete();

        return redirect()->route('admin.master-data.tenaga-kependidikan.index')
            ->with('status', 'Data tenaga kependidikan berhasil dihapus.');
    }

    private function form(TenagaKependidikan $tenagaKependidikan): View
    {
        return view('admin.master-data.tenaga-kependidikan.form', [
            'tenagaKependidikan' => $tenagaKependidikan,
            'pilihanJenisKelamin' => collect(JenisKelamin::cases())->mapWithKeys(fn (JenisKelamin $jenis) => [$jenis->value => $jenis->label()]),
            'pilihanAgama' => collect(Agama::cases())->mapWithKeys(fn (Agama $agama) => [$agama->value => $agama->label()]),
        ]);
    }
}
