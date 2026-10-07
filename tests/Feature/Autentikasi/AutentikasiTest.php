<?php

namespace Tests\Feature\Autentikasi;

use App\Actions\Autentikasi\AutentikasiPengguna;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutentikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_masuk_dapat_ditampilkan(): void
    {
        $this->get(route('admin.masuk'))
            ->assertOk()
            ->assertViewIs('admin.autentikasi.masuk')
            ->assertSee('Nama pengguna atau email');
    }

    public function test_pengguna_dapat_masuk_dengan_nama_pengguna(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->post(route('admin.masuk'), [
            'login' => $pengguna->nama_pengguna,
            'password' => 'password',
        ])->assertRedirect(route('admin.dasbor'));

        $this->assertAuthenticatedAs($pengguna);
    }

    public function test_pengguna_dapat_masuk_dengan_email(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->post(route('admin.masuk'), [
            'login' => $pengguna->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dasbor'));

        $this->assertAuthenticatedAs($pengguna);
    }

    public function test_kata_sandi_salah_ditolak(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->from(route('admin.masuk'))->post(route('admin.masuk'), [
            'login' => $pengguna->nama_pengguna,
            'password' => 'salah-sandi',
        ])->assertRedirect(route('admin.masuk'))
            ->assertSessionHasErrors(['login' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_dapat_masuk(): void
    {
        $pengguna = Pengguna::factory()->nonaktif()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->post(route('admin.masuk'), [
            'login' => $pengguna->nama_pengguna,
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_isian_wajib_divalidasi_dengan_pesan_indonesia(): void
    {
        $this->post(route('admin.masuk'), [])
            ->assertSessionHasErrors([
                'login' => 'Nama pengguna atau email wajib diisi.',
                'password' => 'Kata sandi wajib diisi.',
            ]);
    }

    public function test_percobaan_masuk_dibatasi(): void
    {
        $pengguna = Pengguna::factory()->create();

        for ($i = 0; $i < AutentikasiPengguna::MAKS_PERCOBAAN; $i++) {
            $this->post(route('admin.masuk'), [
                'login' => $pengguna->nama_pengguna,
                'password' => 'salah-sandi',
            ]);
        }

        $this->post(route('admin.masuk'), [
            'login' => $pengguna->nama_pengguna,
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
        $this->assertStringContainsString('Terlalu banyak percobaan masuk', session('errors')->first('login'));
    }

    public function test_pengguna_dapat_keluar(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->post(route('admin.keluar'))
            ->assertRedirect(route('admin.masuk'));

        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_masuk_diarahkan_ke_dasbor(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.masuk'))
            ->assertRedirect(route('admin.dasbor'));
    }
}
