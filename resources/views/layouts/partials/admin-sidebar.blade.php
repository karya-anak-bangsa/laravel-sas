{{--
    Menu area admin. Setiap modul baru menambahkan grupnya di sini dan
    membungkusnya dengan @can sesuai Policy modul tersebut.
--}}
@php($pengguna = auth()->user())

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
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ Str::upper(Str::substr($pengguna->nama_pengguna, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="name">{{ $pengguna->nama_pengguna }}</div>
                <div class="role">{{ $pengguna->peran->map(fn ($peran) => $peran->kode->label())->join(', ') }}</div>
            </div>
        </div>
    </div>
</aside>
