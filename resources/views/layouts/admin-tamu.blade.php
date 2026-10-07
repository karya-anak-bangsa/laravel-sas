<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sw="off">
<head>
    @include('layouts.partials.admin-head')
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="brand-icon">PB</div>
                <div class="brand-name">SAS Puspita Bangsa</div>
            </div>

            @yield('konten')
        </div>
    </div>
</body>
</html>
