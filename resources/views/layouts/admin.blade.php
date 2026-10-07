<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sw="off">
<head>
    @include('layouts.partials.admin-head')
</head>
<body data-shell="admin">
    @include('layouts.partials.admin-sidebar')

    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" type="button" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                @hasSection('judul')
                    <a href="{{ route('admin.dasbor') }}">Dasbor</a>
                    <span class="sep" aria-hidden="true">›</span>
                    <span class="current" aria-current="page">@yield('judul')</span>
                @else
                    <span class="current" aria-current="page">Dasbor</span>
                @endif
            </nav>
        </div>

        <div class="topbar-right">
            <button class="tb-btn theme-toggle" type="button" title="Ganti tema" aria-label="Ganti tema terang/gelap" aria-pressed="false">
                <svg class="theme-icon-light" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg class="theme-icon-dark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>

            <div class="topbar-user">
                <span class="topbar-user-name">{{ auth()->user()->nama_pengguna }}</span>
                <form method="POST" action="{{ route('admin.keluar') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="page-wrapper">
            @if (session('status'))
                <div class="alert alert-success" role="status">
                    <div class="alert-body">{{ session('status') }}</div>
                </div>
            @endif

            @yield('konten')
        </div>

        <footer class="footer">
            <span>&copy; {{ now()->year }} Yayasan Puspita Bangsa Ciputat</span>
            <span>Sistem Akademik Sekolah</span>
        </footer>
    </main>
</body>
</html>
