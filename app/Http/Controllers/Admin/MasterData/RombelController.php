<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusRombel;
use App\Enums\Tingkat;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\RombelRequest;
use App\Models\KonsentrasiKeahlian;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RombelController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Rombel::class);

        // Bawaan: rombel pada tahun ajaran aktif. Pilihan kosong ("0") = semua.
        $idTahunAjaran = $request->has('tahun_ajaran')
            ? $request->integer('tahun_ajaran')
            : $this->idTahunAjaranAktif();
        $idSatuanPendidikan = $request->integer('satuan_pendidikan');

        return view('admin.master-data.rombel.index', [
            'daftarRombel' => Rombel::query()
                ->with(['tahunAjaran', 'satuanPendidikan', 'konsentrasiKeahlian'])
                ->when($idTahunAjaran, fn ($query) => $query->where('id_tahun_ajaran', $idTahunAjaran))
                ->when($idSatuanPendidikan, fn ($query) => $query->where('id_satuan_pendidikan', $idSatuanPendidikan))
                ->orderByDesc('id_tahun_ajaran')
                ->orderBy('id_satuan_pendidikan')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->paginate(20)
                ->withQueryString(),
            'pilihanTahunAjaran' => $this->pilihanTahunAjaran(),
            'pilihanSatuanPendidikan' => $this->pilihanSatuanPendidikan(),
            'idTahunAjaran' => $idTahunAjaran,
            'idSatuanPendidikan' => $idSatuanPendidikan,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Rombel::class);

        return $this->form(new Rombel(['id_tahun_ajaran' => $this->idTahunAjaranAktif()]));
    }

    public function store(RombelRequest $request): RedirectResponse
    {
        Rombel::create($request->validated());

        return redirect()->route('admin.master-data.rombel.index', ['tahun_ajaran' => $request->integer('id_tahun_ajaran')])
            ->with('status', 'Rombel berhasil ditambahkan.');
    }

    public function edit(Rombel $rombel): View
    {
        Gate::authorize('update', $rombel);

        return $this->form($rombel);
    }

    public function update(RombelRequest $request, Rombel $rombel): RedirectResponse
    {
        $rombel->update($request->validated());

        return redirect()->route('admin.master-data.rombel.index', ['tahun_ajaran' => $rombel->id_tahun_ajaran])
            ->with('status', 'Rombel berhasil diperbarui.');
    }

    public function destroy(Rombel $rombel, HapusRombel $hapus): RedirectResponse
    {
        Gate::authorize('delete', $rombel);

        $hapus->handle($rombel);

        return redirect()->route('admin.master-data.rombel.index', ['tahun_ajaran' => $rombel->id_tahun_ajaran])
            ->with('status', 'Rombel berhasil dihapus.');
    }

    private function form(Rombel $rombel): View
    {
        return view('admin.master-data.rombel.form', [
            'rombel' => $rombel,
            'pilihanTahunAjaran' => $this->pilihanTahunAjaran(),
            'pilihanSatuanPendidikan' => $this->pilihanSatuanPendidikan(),
            'pilihanTingkat' => collect(Tingkat::cases())->mapWithKeys(fn (Tingkat $tingkat) => [$tingkat->value => $tingkat->label()]),
            'pilihanKonsentrasiKeahlian' => KonsentrasiKeahlian::query()
                ->orderBy('nama')
                ->get()
                ->mapWithKeys(fn (KonsentrasiKeahlian $konsentrasi) => [
                    $konsentrasi->id_konsentrasi_keahlian => "{$konsentrasi->singkatan} — {$konsentrasi->nama}",
                ]),
        ]);
    }

    private function idTahunAjaranAktif(): ?int
    {
        return Semester::query()->aktif()->value('id_tahun_ajaran');
    }

    /**
     * @return Collection<int, string>
     */
    private function pilihanTahunAjaran(): Collection
    {
        return TahunAjaran::query()->orderByDesc('nama')->pluck('nama', 'id_tahun_ajaran');
    }

    /**
     * @return Collection<int, string>
     */
    private function pilihanSatuanPendidikan(): Collection
    {
        return SatuanPendidikan::query()->orderBy('nama')->pluck('nama', 'id_satuan_pendidikan');
    }
}
