<?php

namespace App\Http\Controllers\Admin\Pengguna;

use App\Actions\Pengguna\HapusPengguna;
use App\Actions\Pengguna\SimpanPengguna;
use App\Enums\KodePeran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pengguna\PenggunaRequest;
use App\Models\Pengguna;
use App\Models\TenagaPendidik;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Pengguna::class);

        $cari = $request->string('cari')->trim()->value();

        return view('admin.pengguna.index', [
            'daftarPengguna' => Pengguna::query()
                ->with(['peran', 'tenagaPendidik'])
                ->when($cari !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('nama_pengguna', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%")))
                ->orderBy('nama_pengguna')
                ->paginate(20)
                ->withQueryString(),
            'cari' => $cari,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Pengguna::class);

        return $this->form(new Pengguna(['aktif' => true]));
    }

    public function store(PenggunaRequest $request, SimpanPengguna $simpan): RedirectResponse
    {
        $simpan->handle(new Pengguna, $request->validated());

        return redirect()->route('admin.pengguna.index')
            ->with('status', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(Pengguna $pengguna): View
    {
        Gate::authorize('update', $pengguna);

        return $this->form($pengguna->load(['peran', 'tenagaPendidik']));
    }

    public function update(PenggunaRequest $request, Pengguna $pengguna, SimpanPengguna $simpan): RedirectResponse
    {
        $simpan->handle($pengguna, $request->validated());

        return redirect()->route('admin.pengguna.index')
            ->with('status', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, Pengguna $pengguna, HapusPengguna $hapus): RedirectResponse
    {
        Gate::authorize('delete', $pengguna);

        $hapus->handle($pengguna, $request->user());

        return redirect()->route('admin.pengguna.index')
            ->with('status', 'Akun pengguna berhasil dihapus.');
    }

    private function form(Pengguna $pengguna): View
    {
        return view('admin.pengguna.form', [
            'pengguna' => $pengguna,
            'pilihanPeran' => KodePeran::daftarTetap(),
            'peranTerpilih' => old('peran', $pengguna->exists ? $pengguna->peran->map(fn ($peran) => $peran->kode->value)->all() : []),
            // Tenaga pendidik yang belum punya akun, ditambah yang sudah tertaut ke akun ini.
            'pilihanTenagaPendidik' => TenagaPendidik::query()
                ->where(fn (Builder $query) => $query
                    ->whereNull('id_pengguna')
                    ->when($pengguna->exists, fn (Builder $query) => $query->orWhere('id_pengguna', $pengguna->id_pengguna)))
                ->orderBy('nama_lengkap')
                ->pluck('nama_lengkap', 'id_tenaga_pendidik'),
        ]);
    }
}
