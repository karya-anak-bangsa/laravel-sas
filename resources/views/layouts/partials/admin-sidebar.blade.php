{{--
    Menu area admin. Setiap modul baru menambahkan grupnya di sini dan
    membungkusnya dengan @can sesuai Policy modul tersebut.
--}}
@php
    $pengguna = auth()->user();

    // Hanya item yang boleh dilihat pengguna (Policy viewAny) yang ditampilkan.
    $menuMasterData = collect([
        ['teks' => 'Satuan Pendidikan', 'route' => 'admin.master-data.satuan-pendidikan.index', 'aktif' => 'admin.master-data.satuan-pendidikan.*', 'model' => App\Models\SatuanPendidikan::class],
        ['teks' => 'Tahun Ajaran', 'route' => 'admin.master-data.tahun-ajaran.index', 'aktif' => 'admin.master-data.tahun-ajaran.*', 'model' => App\Models\TahunAjaran::class],
        ['teks' => 'Rombel', 'route' => 'admin.master-data.rombel.index', 'aktif' => 'admin.master-data.rombel.*', 'model' => App\Models\Rombel::class],
        ['teks' => 'Tenaga Pendidik', 'route' => 'admin.master-data.tenaga-pendidik.index', 'aktif' => 'admin.master-data.tenaga-pendidik.*', 'model' => App\Models\TenagaPendidik::class],
        ['teks' => 'Penugasan', 'route' => 'admin.master-data.penugasan.index', 'aktif' => 'admin.master-data.penugasan.*', 'model' => App\Models\Penugasan::class],
        ['teks' => 'Bidang Keahlian', 'route' => 'admin.master-data.bidang-keahlian.index', 'aktif' => 'admin.master-data.bidang-keahlian.*', 'model' => App\Models\BidangKeahlian::class],
        ['teks' => 'Program Keahlian', 'route' => 'admin.master-data.program-keahlian.index', 'aktif' => 'admin.master-data.program-keahlian.*', 'model' => App\Models\ProgramKeahlian::class],
        ['teks' => 'Konsentrasi Keahlian', 'route' => 'admin.master-data.konsentrasi-keahlian.index', 'aktif' => 'admin.master-data.konsentrasi-keahlian.*', 'model' => App\Models\KonsentrasiKeahlian::class],
    ])->filter(fn (array $item) => $pengguna->can('viewAny', $item['model']));

    $masterDataAktif = $menuMasterData->contains(fn (array $item) => request()->routeIs($item['aktif']));
@endphp

<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
    <div class="sidebar-brand">
        <div class="brand-icon">PB</div>
        <div class="brand-name">SAS <small>Puspita Bangsa</small></div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group">
            <div class="nav-label">Umum</div>
            <a @class(['nav-link', 'active' => request()->routeIs('admin.dasbor')]) href="{{ route('admin.dasbor') }}" @if (request()->routeIs('admin.dasbor')) aria-current="page" @endif>
                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="4" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="10" width="7" height="11" rx="1.5"/></svg>
                <span class="nav-text">Dasbor</span>
            </a>
        </div>

        @if ($menuMasterData->isNotEmpty())
            <div class="nav-group">
                <div class="nav-label">Data Sekolah</div>
                <div @class(['nav-tree', 'open' => $masterDataAktif, 'has-active' => $masterDataAktif])>
                    <button type="button" class="nav-link nav-toggle" aria-expanded="{{ $masterDataAktif ? 'true' : 'false' }}">
                        <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>
                        <span class="nav-text">Master Data</span>
                        <svg class="nav-chev" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 4l4 4-4 4"/></svg>
                    </button>
                    <div class="nav-sub">
                        <div class="nav-sub-inner">
                            @foreach ($menuMasterData as $item)
                                @php($itemAktif = request()->routeIs($item['aktif']))
                                <a @class(['nav-sublink', 'active' => $itemAktif]) href="{{ route($item['route']) }}" @if ($itemAktif) aria-current="page" @endif>{{ $item['teks'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ Str::upper(Str::substr($pengguna->nama_pengguna, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="name">{{ $pengguna->nama_pengguna }}</div>
                <div class="role">{{ $pengguna->kodePeran()->map(fn ($kode) => $kode->label())->join(', ') }}</div>
            </div>
        </div>
    </div>
</aside>
