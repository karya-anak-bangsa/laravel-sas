<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusOrangTuaWali;
use App\Actions\MasterData\SimpanOrangTuaWali;
use App\Enums\HubunganOrangTua;
use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\OrangTuaWaliRequest;
use App\Models\Murid;
use App\Models\OrangTuaWali;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrangTuaWaliController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', OrangTuaWali::class);

        $cari = $request->string('cari')->trim()->value();

        return view('admin.master-data.orang-tua-wali.index', [
            'daftarOrangTuaWali' => OrangTuaWali::query()
                ->with('murid')
                ->when($cari !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nomor_hp', 'like', "%{$cari}%")))
                ->orderBy('nama')
                ->orderBy('id_orang_tua_wali')
                ->paginate(20)
                ->withQueryString(),
            'cari' => $cari,
        ]);
    }

    /**
     * Dengan ?murid=ID, data baru langsung ditautkan ke murid tersebut.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', OrangTuaWali::class);

        $murid = $request->filled('murid') ? Murid::query()->findOrFail($request->integer('murid')) : null;

        if ($murid) {
            Gate::authorize('update', $murid);
        }

        return $this->form(new OrangTuaWali, $murid, HubunganOrangTua::tryFrom($request->string('hubungan')->value()));
    }

    public function store(OrangTuaWaliRequest $request, SimpanOrangTuaWali $simpan): RedirectResponse
    {
        $simpan->handle(new OrangTuaWali, $request->validated());

        if ($request->filled('id_murid')) {
            return redirect()->route('admin.master-data.murid.edit', $request->integer('id_murid'))
                ->with('status', 'Orang tua/wali berhasil ditambahkan dan ditautkan.');
        }

        return redirect()->route('admin.master-data.orang-tua-wali.index')
            ->with('status', 'Data orang tua/wali berhasil ditambahkan.');
    }

    public function edit(OrangTuaWali $orangTuaWali): View
    {
        Gate::authorize('update', $orangTuaWali);

        return $this->form($orangTuaWali->load('murid'));
    }

    public function update(OrangTuaWaliRequest $request, OrangTuaWali $orangTuaWali, SimpanOrangTuaWali $simpan): RedirectResponse
    {
        $simpan->handle($orangTuaWali, $request->validated());

        return redirect()->route('admin.master-data.orang-tua-wali.index')
            ->with('status', 'Data orang tua/wali berhasil diperbarui.');
    }

    public function destroy(OrangTuaWali $orangTuaWali, HapusOrangTuaWali $hapus): RedirectResponse
    {
        Gate::authorize('delete', $orangTuaWali);

        $hapus->handle($orangTuaWali);

        return redirect()->route('admin.master-data.orang-tua-wali.index')
            ->with('status', 'Data orang tua/wali berhasil dihapus.');
    }

    private function form(OrangTuaWali $orangTuaWali, ?Murid $murid = null, ?HubunganOrangTua $hubungan = null): View
    {
        return view('admin.master-data.orang-tua-wali.form', [
            'orangTuaWali' => $orangTuaWali,
            'murid' => $murid,
            'hubungan' => $hubungan,
            'pilihanHubungan' => $this->pilihan(HubunganOrangTua::cases()),
            'pilihanPendidikan' => $this->pilihan(PendidikanOrangTua::cases()),
            'pilihanPekerjaan' => $this->pilihan(PekerjaanOrangTua::cases()),
            'pilihanPenghasilan' => $this->pilihan(PenghasilanOrangTua::cases()),
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
