<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('judul')@yield('judul') | @endif{{ config('app.name') }}</title>
    @fonts('instrument-sans')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-200">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('beranda') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-lg bg-emerald-600 text-sm font-semibold text-white">PB</span>
                <span class="leading-tight">
                    <span class="block font-semibold">Yayasan Puspita Bangsa</span>
                    <span class="block text-sm text-slate-500">Ciputat</span>
                </span>
            </a>
            @yield('navigasi')
        </div>
    </header>

    <main class="flex-1">
        @yield('konten')
    </main>

    <footer class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-slate-500">
            &copy; {{ now()->year }} Yayasan Puspita Bangsa Ciputat
        </div>
    </footer>
</body>
</html>
