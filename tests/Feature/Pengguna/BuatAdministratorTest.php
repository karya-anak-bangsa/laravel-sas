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
            ->expectsQuestion('Nama pengguna', 'admin')
            ->expectsQuestion('Email (opsional)', 'admin@sekolah.test')
            ->expectsQuestion('Kata sandi', 'rahasia123')
            ->expectsQuestion('Ulangi kata sandi', 'rahasia123')
            ->assertSuccessful();

        $pengguna = Pengguna::query()->where('nama_pengguna', 'admin')->firstOrFail();

        $this->assertSame('admin@sekolah.test', $pengguna->email);
        $this->assertTrue($pengguna->adalahAdministrator());
    }

    public function test_perintah_gagal_jika_konfirmasi_kata_sandi_tidak_cocok(): void
    {
        $this->artisan('pengguna:buat-administrator')
            ->expectsQuestion('Nama pengguna', 'admin')
            ->expectsQuestion('Email (opsional)', '')
            ->expectsQuestion('Kata sandi', 'rahasia123')
            ->expectsQuestion('Ulangi kata sandi', 'berbeda123')
            ->assertFailed();

        $this->assertDatabaseMissing('tb_pengguna', ['nama_pengguna' => 'admin']);
    }

    public function test_perintah_gagal_jika_nama_pengguna_sudah_dipakai(): void
    {
        Pengguna::factory()->create(['nama_pengguna' => 'admin']);

        $this->artisan('pengguna:buat-administrator')
            ->expectsQuestion('Nama pengguna', 'admin')
            ->expectsQuestion('Email (opsional)', '')
            ->expectsQuestion('Kata sandi', 'rahasia123')
            ->expectsQuestion('Ulangi kata sandi', 'rahasia123')
            ->assertFailed();

        $this->assertSame(1, Pengguna::query()->where('nama_pengguna', 'admin')->count());
    }
}
