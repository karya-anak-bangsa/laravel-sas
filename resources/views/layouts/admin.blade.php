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
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
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
                <i class="theme-icon-light fa-solid fa-sun" aria-hidden="true"></i>
                <i class="theme-icon-dark fa-regular fa-moon" aria-hidden="true"></i>
            </button>

            <div class="topbar-user">
                <span class="topbar-user-name">{{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('admin.keluar') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Keluar</button>
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

            @if (session('galat'))
                <div class="alert alert-error" role="alert">
                    <div class="alert-body">{{ session('galat') }}</div>
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
