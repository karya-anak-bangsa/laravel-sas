<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\SimpanPenugasan;
use App\Enums\KodePeran;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\PenugasanRequest;
use App\Models\KonsentrasiKeahlian;
use App\Models\Penugasan;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PenugasanController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Penugasan::class);

        $idTahunAjaran = $request->has('tahun_ajaran')
            ? $request->integer('tahun_ajaran')
            : $this->idTahunAjaranAktif();
        $kodePeran = KodePeran::tryFrom($request->string('peran')->value());

        return view('admin.master-data.penugasan.index', [
            'daftarPenugasan' => Penugasan::query()
                ->with(['tenagaPendidik', 'tahunAjaran', 'peran', 'rombel', 'konsentrasiKeahlian', 'satuanPendidikan'])
                ->when($idTahunAjaran, fn (Builder $query) => $query->where('id_tahun_ajaran', $idTahunAjaran))
                ->when($kodePeran, fn (Builder $query) => $query->whereHas('peran', fn (Builder $query) => $query->where('kode', $kodePeran)))
                ->orderByDesc('id_tahun_ajaran')
                ->orderBy('id_peran')
                ->orderBy('id_penugasan')
                ->paginate(20)
                ->withQueryString(),
            'pilihanTahunAjaran' => TahunAjaran::query()->orderByDesc('nama')->pluck('nama', 'id_tahun_ajaran'),
            'pilihanPeran' => $this->pilihanPeran(),
            'saringan' => ['tahun_ajaran' => $idTahunAjaran, 'peran' => $kodePeran],
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        Gate::authorize('create', Penugasan::class);

        $tahunAjaran = TahunAjaran::query()->find($request->integer('tahun_ajaran') ?: $this->idTahunAjaranAktif());

        if (! $tahunAjaran) {
            return redirect()->route('admin.master-data.penugasan.index')
                ->with('galat', 'Pilih tahun ajaran terlebih dahulu.');
        }

        return $this->form(new Penugasan(['id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran]), $tahunAjaran);
    }

    public function store(PenugasanRequest $request, SimpanPenugasan $simpan): RedirectResponse
    {
        $penugasan = $simpan->handle(new Penugasan, $request->validated());

        return redirect()->route('admin.master-data.penugasan.index', ['tahun_ajaran' => $penugasan->id_tahun_ajaran])
            ->with('status', 'Penugasan berhasil ditambahkan.');
    }

    public function edit(Penugasan $penugasan): View
    {
        Gate::authorize('update', $penugasan);

        $penugasan->load(['peran', 'tahunAjaran']);

        return $this->form($penugasan, $penugasan->tahunAjaran ?? abort(404));
    }

    public function update(PenugasanRequest $request, Penugasan $penugasan, SimpanPenugasan $simpan): RedirectResponse
    {
        $simpan->handle($penugasan, $request->validated());

        return redirect()->route('admin.master-data.penugasan.index', ['tahun_ajaran' => $penugasan->id_tahun_ajaran])
            ->with('status', 'Penugasan berhasil diperbarui.');
    }

    public function destroy(Penugasan $penugasan): RedirectResponse
    {
        Gate::authorize('delete', $penugasan);

        $penugasan->delete();

        return redirect()->route('admin.master-data.penugasan.index', ['tahun_ajaran' => $penugasan->id_tahun_ajaran])
            ->with('status', 'Penugasan berhasil dihapus.');
    }

    private function form(Penugasan $penugasan, TahunAjaran $tahunAjaran): View
    {
        return view('admin.master-data.penugasan.form', [
            'penugasan' => $penugasan,
            'tahunAjaran' => $tahunAjaran,
            'pilihanPeran' => $this->pilihanPeran(),
            'pilihanTenagaPendidik' => TenagaPendidik::query()
                ->with('satuanPendidikan')
                ->orderBy('nama_lengkap')
                ->get()
                ->mapWithKeys(fn (TenagaPendidik $tenagaPendidik) => [
                    $tenagaPendidik->id_tenaga_pendidik => "{$tenagaPendidik->nama_lengkap} ({$tenagaPendidik->satuanPendidikan?->nama})",
                ]),
            'pilihanRombel' => Rombel::query()
                ->where('id_tahun_ajaran', $tahunAjaran->id_tahun_ajaran)
                ->orderBy('id_satuan_pendidikan')
                ->orderBy('tingkat')
                ->orderBy('nama')
                ->pluck('nama', 'id_rombel'),
            'pilihanKonsentrasiKeahlian' => KonsentrasiKeahlian::query()->orderBy('nama')->pluck('nama', 'id_konsentrasi_keahlian'),
            'pilihanSatuanPendidikan' => SatuanPendidikan::query()->orderBy('nama')->pluck('nama', 'id_satuan_pendidikan'),
        ]);
    }

    private function idTahunAjaranAktif(): ?int
    {
        return Semester::query()->aktif()->value('id_tahun_ajaran');
    }

    /**
     * @return array<string, string>
     */
    private function pilihanPeran(): array
    {
        return collect(KodePeran::daftarKontekstual())
            ->mapWithKeys(fn (KodePeran $kode) => [$kode->value => $kode->label()])
            ->all();
    }
}
