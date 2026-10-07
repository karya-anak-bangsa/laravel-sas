<?php

namespace App\Actions\Autentikasi;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Memasukkan pengguna dengan nama pengguna atau email.
 *
 * Hanya akun aktif yang dapat masuk. Percobaan gagal dibatasi per
 * kombinasi login + alamat IP.
 */
class AutentikasiPengguna
{
    public const MAKS_PERCOBAAN = 5;

    /**
     * @throws ValidationException
     */
    public function handle(string $login, string $password, bool $ingat, string $ip): void
    {
        $kunci = $this->kunciPembatas($login, $ip);

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            throw ValidationException::withMessages([
                'login' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($kunci)]),
            ]);
        }

        $kolom = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nama_pengguna';

        if (! Auth::attempt([$kolom => $login, 'password' => $password, 'aktif' => true], $ingat)) {
            RateLimiter::hit($kunci);

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($kunci);
    }

    private function kunciPembatas(string $login, string $ip): string
    {
        return 'masuk:'.sha1(Str::lower($login).'|'.$ip);
    }
}
