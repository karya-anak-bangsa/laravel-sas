<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@hasSection('judul')@yield('judul') | @endif{{ config('app.name') }}</title>
{{-- Terapkan tema terang/gelap sebelum halaman dirender agar tidak berkedip. --}}
<script>(function(){try{var t=localStorage.getItem('theme');var d=window.matchMedia('(prefers-color-scheme: dark)').matches;document.documentElement.setAttribute('data-theme',t||(d?'dark':'light'));}catch(e){}})();</script>
@fonts('inter')
@vite(['resources/js/admin.js', 'resources/css/admin.css'])
