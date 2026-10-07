<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\SimpanMurid;
use App\Enums\Agama;
use App\Enums\BerkebutuhanKhusus;
use App\Enums\JenisKelamin;
use App\Enums\ModaTransportasi;
use App\Enums\TempatTinggal;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\MuridRequest;
use App\Models\Murid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MuridController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Murid::class);

        $cari = $request->string('cari')->trim()->value();

        return view('admin.master-data.murid.index', [
            'daftarMurid' => Murid::query()
                ->when($cari !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('nama_lengkap', 'like', "%{$cari}%")
                    ->orWhere('nisn', 'like', "{$cari}%")
                    ->orWhere('nik', 'like', "{$cari}%")))
                ->orderBy('nama_lengkap')
                ->orderBy('id_murid')
                ->paginate(20)
                ->withQueryString(),
            'cari' => $cari,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Murid::class);

        return $this->form(new Murid);
    }

    public function store(MuridRequest $request, SimpanMurid $simpan): RedirectResponse
    {
        $murid = $simpan->handle(new Murid, $request->validated());

        return redirect()->route('admin.master-data.murid.edit', $murid)
            ->with('status', 'Data murid berhasil ditambahkan.');
    }

    public function edit(Murid $murid): View
    {
        Gate::authorize('update', $murid);

        return $this->form($murid->load('berkebutuhanKhusus'));
    }

    public function update(MuridRequest $request, Murid $murid, SimpanMurid $simpan): RedirectResponse
    {
        $simpan->handle($murid, $request->validated());

        return redirect()->route('admin.master-data.murid.edit', $murid)
            ->with('status', 'Data murid berhasil diperbarui.');
    }

    public function destroy(Murid $murid): RedirectResponse
    {
        Gate::authorize('delete', $murid);

        $murid->delete();

        return redirect()->route('admin.master-data.murid.index')
            ->with('status', 'Data murid berhasil dihapus.');
    }

    private function form(Murid $murid): View
    {
        return view('admin.master-data.murid.form', [
            'murid' => $murid,
            'pilihanJenisKelamin' => $this->pilihan(JenisKelamin::cases()),
            'pilihanAgama' => $this->pilihan(Agama::cases()),
            'pilihanModaTransportasi' => $this->pilihan(ModaTransportasi::cases()),
            'pilihanTempatTinggal' => $this->pilihan(TempatTinggal::cases()),
            'pilihanBerkebutuhanKhusus' => BerkebutuhanKhusus::cases(),
            'kebutuhanTerpilih' => old(
                'berkebutuhan_khusus',
                $murid->exists ? $murid->berkebutuhanKhusus->map(fn ($baris) => $baris->kode->value)->all() : [],
            ),
        ]);
    }

    /**
     * Pilihan select dari enum yang memiliki method label().
     *
     * @param  list<\BackedEnum>  $kasus
     * @return Collection<string, string>
     */
    private function pilihan(array $kasus): Collection
    {
        return collect($kasus)->mapWithKeys(fn ($enum) => [$enum->value => $enum->label()]);
    }
}
