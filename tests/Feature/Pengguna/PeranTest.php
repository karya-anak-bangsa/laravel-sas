<?php

namespace Tests\Feature\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PeranTest extends TestCase
{
    use RefreshDatabase;

    public function test_setiap_kode_peran_tersedia_di_tb_peran(): void
    {
        foreach (KodePeran::cases() as $kode) {
            $this->assertDatabaseHas('tb_peran', ['kode' => $kode->value, 'nama' => $kode->label()]);
        }

        $this->assertSame(count(KodePeran::cases()), Peran::query()->count());
    }

    public function test_satu_pengguna_dapat_memiliki_banyak_peran(): void
    {
        $pengguna = Pengguna::factory()
            ->denganPeran(KodePeran::TenagaPendidik, KodePeran::WaliKelas)
            ->create();

        $this->assertCount(2, $pengguna->peran);
        $this->assertTrue($pengguna->memilikiPeran(KodePeran::WaliKelas));
        $this->assertTrue($pengguna->memilikiPeran(KodePeran::KetuaJurusan, KodePeran::TenagaPendidik));
        $this->assertFalse($pengguna->memilikiPeran(KodePeran::Administrator));
    }

    public function test_peran_yang_sama_tidak_dapat_diberikan_dua_kali(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();
        $peran = Peran::query()->where('kode', KodePeran::TenagaPendidik)->firstOrFail();

        $this->expectException(UniqueConstraintViolationException::class);

        $pengguna->peran()->attach($peran->id_peran);
    }

    public function test_akses_admin_hanya_untuk_akun_aktif_yang_memiliki_peran(): void
    {
        $tanpaPeran = Pengguna::factory()->create();
        $denganPeran = Pengguna::factory()->denganPeran(KodePeran::TenagaKependidikan)->create();
        $nonaktif = Pengguna::factory()->nonaktif()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->assertFalse(Gate::forUser($tanpaPeran)->allows('akses-admin'));
        $this->assertTrue(Gate::forUser($denganPeran)->allows('akses-admin'));
        $this->assertFalse(Gate::forUser($nonaktif)->allows('akses-admin'));
    }

    public function test_administrator_aktif_lolos_semua_gate(): void
    {
        Gate::define('aksi-uji', fn () => false);

        $admin = Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
        $adminNonaktif = Pengguna::factory()->nonaktif()->denganPeran(KodePeran::Administrator)->create();

        $this->assertTrue(Gate::forUser($admin)->allows('aksi-uji'));
        $this->assertFalse(Gate::forUser($adminNonaktif)->allows('aksi-uji'));
        $this->assertFalse(Gate::forUser($adminNonaktif)->allows('akses-admin'));
    }
}
