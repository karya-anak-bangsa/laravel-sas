<?php

namespace Tests\Feature\Pengguna;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuatAdministratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_perintah_membuat_akun_administrator(): void
    {
        $this->artisan('pengguna:buat-administrator')
            ->expectsQuestion('Email', 'admin@sekolah.test')
            ->expectsQuestion('Password', 'rahasia123')
            ->expectsQuestion('Ulangi password', 'rahasia123')
            ->assertSuccessful();

        $pengguna = Pengguna::query()->where('email', 'admin@sekolah.test')->firstOrFail();

        $this->assertTrue($pengguna->adalahAdministrator());
    }

    public function test_perintah_gagal_jika_konfirmasi_password_tidak_cocok(): void
    {
        $this->artisan('pengguna:buat-administrator')
            ->expectsQuestion('Email', 'admin@sekolah.test')
            ->expectsQuestion('Password', 'rahasia123')
            ->expectsQuestion('Ulangi password', 'berbeda123')
            ->assertFailed();

        $this->assertDatabaseMissing('tb_pengguna', ['email' => 'admin@sekolah.test']);
    }

    public function test_perintah_gagal_jika_email_sudah_dipakai(): void
    {
        Pengguna::factory()->create(['email' => 'admin@sekolah.test']);

        $this->artisan('pengguna:buat-administrator')
            ->expectsQuestion('Email', 'admin@sekolah.test')
            ->expectsQuestion('Password', 'rahasia123')
            ->expectsQuestion('Ulangi password', 'rahasia123')
            ->assertFailed();

        $this->assertSame(1, Pengguna::query()->where('email', 'admin@sekolah.test')->count());
    }
}
