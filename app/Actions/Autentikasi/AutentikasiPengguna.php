<?php

namespace App\Actions\Autentikasi;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Memasukkan pengguna dengan email.
 *
 * Hanya akun aktif yang dapat masuk. Percobaan gagal dibatasi per
 * kombinasi email + alamat IP.
 */
class AutentikasiPengguna
{
    public const MAKS_PERCOBAAN = 5;

    /**
     * @throws ValidationException
     */
    public function handle(string $email, string $password, bool $ingat, string $ip): void
    {
        $kunci = $this->kunciPembatas($email, $ip);

        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($kunci)]),
            ]);
        }

        if (! Auth::attempt(['email' => $email, 'password' => $password, 'aktif' => true], $ingat)) {
            RateLimiter::hit($kunci);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($kunci);
    }

    private function kunciPembatas(string $email, string $ip): string
    {
        return 'masuk:'.sha1(Str::lower($email).'|'.$ip);
    }
}
