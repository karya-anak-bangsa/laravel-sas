<?php

namespace App\Providers;

use App\Models\Pengguna;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->daftarkanGate();
    }

    /**
     * Gate umum. Otorisasi per modul ditulis sebagai Policy di app/Policies.
     */
    private function daftarkanGate(): void
    {
        // Administrator aktif boleh melakukan semua aksi.
        Gate::before(fn (Pengguna $pengguna) => $pengguna->aktif && $pengguna->adalahAdministrator() ? true : null);

        // Area admin hanya untuk akun aktif yang memiliki paling sedikit satu peran.
        Gate::define('akses-admin', fn (Pengguna $pengguna) => $pengguna->aktif && $pengguna->peran->isNotEmpty());
    }
}
