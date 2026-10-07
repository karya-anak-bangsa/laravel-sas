<?php

namespace App\Policies;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;

/**
 * Kebijakan dasar master data: hanya Administrator yang boleh mengelola.
 *
 * Administrator aktif sudah diloloskan oleh Gate::before (AppServiceProvider),
 * sehingga setiap method di sini menolak peran lain. Policy turunan
 * meng-override method tertentu jika suatu peran perlu akses baca.
 */
abstract class KebijakanMasterData
{
    public function viewAny(Pengguna $pengguna): bool
    {
        return false;
    }

    public function view(Pengguna $pengguna, Model $model): bool
    {
        return false;
    }

    public function create(Pengguna $pengguna): bool
    {
        return false;
    }

    public function update(Pengguna $pengguna, Model $model): bool
    {
        return false;
    }

    public function delete(Pengguna $pengguna, Model $model): bool
    {
        return false;
    }
}
