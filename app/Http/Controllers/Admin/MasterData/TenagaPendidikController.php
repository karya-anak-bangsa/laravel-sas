<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Enums\JenjangPendidikan;
use App\Enums\StatusTenagaPendidik;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\TenagaPendidikRequest;
use App\Models\SatuanPendidikan;
use App\Models\TenagaPendidik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenagaPendidikController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TenagaPendidik::class);

        $cari = $request->string('cari')->trim()->value();
        $idSatuanPendidikan = $request->integer('satuan_pendidikan');
        $status = StatusTenagaPendidik::tryFrom($request->string('status')->value());

        return view('admin.master-data.tenaga-pendidik.index', [
            'daftarTenagaPendidik' => TenagaPendidik::query()
                ->with(['satuanPendidikan', 'pengguna'])
                ->when($cari !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('nama_lengkap', 'like', "%{$cari}%")
                    ->orWhere('nuptk', 'like', "{$cari}%")))
                ->when($idSatuanPendidikan, fn ($query) => $query->where('id_satuan_pendidikan', $idSatuanPendidikan))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->orderBy('nama_lengkap')
                ->orderBy('id_tenaga_pendidik')
                ->paginate(20)
                ->withQueryString(),
            'pilihanSatuanPendidikan' => SatuanPendidikan::query()->orderBy('nama')->pluck('nama', 'id_satuan_pendidikan'),
            'pilihanStatus' => $this->pilihanStatus(),
            'saringan' => ['cari' => $cari, 'satuan_pendidikan' => $idSatuanPendidikan, 'status' => $status],
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', TenagaPendidik::class);

        return $this->form(new TenagaPendidik(['masa_kerja_tahun' => 0, 'masa_kerja_bulan' => 0]));
    }

    public function store(TenagaPendidikRequest $request): RedirectResponse
    {
        TenagaPendidik::create($request->validated());

        return redirect()->route('admin.master-data.tenaga-pendidik.index')
            ->with('status', 'Data tenaga pendidik berhasil ditambahkan.');
    }

    public function edit(TenagaPendidik $tenagaPendidik): View
    {
        Gate::authorize('update', $tenagaPendidik);

        return $this->form($tenagaPendidik);
    }

    public function update(TenagaPendidikRequest $request, TenagaPendidik $tenagaPendidik): RedirectResponse
    {
        $tenagaPendidik->update($request->validated());

        return redirect()->route('admin.master-data.tenaga-pendidik.index')
            ->with('status', 'Data tenaga pendidik berhasil diperbarui.');
    }

    public function destroy(TenagaPendidik $tenagaPendidik): RedirectResponse
    {
        Gate::authorize('delete', $tenagaPendidik);

        $tenagaPendidik->delete();

        return redirect()->route('admin.master-data.tenaga-pendidik.index')
            ->with('status', 'Data tenaga pendidik berhasil dihapus.');
    }

    private function form(TenagaPendidik $tenagaPendidik): View
    {
        return view('admin.master-data.tenaga-pendidik.form', [
            'tenagaPendidik' => $tenagaPendidik,
            'pilihanSatuanPendidikan' => SatuanPendidikan::query()->orderBy('nama')->pluck('nama', 'id_satuan_pendidikan'),
            'pilihanStatus' => $this->pilihanStatus(),
            'pilihanPendidikan' => collect(JenjangPendidikan::cases())
                ->mapWithKeys(fn (JenjangPendidikan $jenjang) => [$jenjang->value => $jenjang->label()]),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function pilihanStatus(): array
    {
        return collect(StatusTenagaPendidik::cases())
            ->mapWithKeys(fn (StatusTenagaPendidik $status) => [$status->value => "{$status->label()} ({$status->keterangan()})"])
            ->all();
    }
}
