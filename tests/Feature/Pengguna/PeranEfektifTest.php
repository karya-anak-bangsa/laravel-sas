<?php

namespace Tests\Feature\Pengguna;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Penugasan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PeranEfektifTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Akun tenaga pendidik beserta data tenaga pendidiknya.
     *
     * @return array{Pengguna, TenagaPendidik}
     */
    private function akunTenagaPendidik(KodePeran ...$peranTetap): array
    {
        $pengguna = Pengguna::factory()->denganPeran(...$peranTetap)->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();
        $tenagaPendidik->forceFill(['id_pengguna' => $pengguna->id_pengguna])->save();

        return [$pengguna, $tenagaPendidik];
    }

    public function test_penugasan_di_tahun_ajaran_aktif_menambah_peran(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik(KodePeran::TenagaPendidik);
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        Penugasan::factory()->waliKelas()->for($tenagaPendidik)->for($aktif)->create();

        $this->assertTrue($pengguna->memilikiPeran(KodePeran::WaliKelas));
        $this->assertSame(
            [KodePeran::WaliKelas, KodePeran::TenagaPendidik],
            $pengguna->kodePeran()->all(),
        );
    }

    public function test_penugasan_di_tahun_ajaran_tidak_aktif_tidak_dihitung(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik(KodePeran::TenagaPendidik);
        TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        $lama = TahunAjaran::factory()->denganSemester()->create();
        Penugasan::factory()->for($tenagaPendidik)->for($lama)->create();

        $this->assertFalse($pengguna->memilikiPeran(KodePeran::KepalaSekolah));
    }

    public function test_penugasan_yang_dihapus_tidak_dihitung(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik(KodePeran::TenagaPendidik);
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Genap)->create();
        Penugasan::factory()->ketuaJurusan()->for($tenagaPendidik)->for($aktif)->create()->delete();

        $this->assertFalse($pengguna->memilikiPeran(KodePeran::KetuaJurusan));
    }

    public function test_penugasan_dari_tenaga_pendidik_yang_dihapus_tidak_dihitung(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik(KodePeran::TenagaPendidik);
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Genap)->create();
        Penugasan::factory()->for($tenagaPendidik)->for($aktif)->create();
        $tenagaPendidik->delete();

        $this->assertFalse($pengguna->memilikiPeran(KodePeran::KepalaSekolah));
    }

    public function test_peran_dari_penugasan_saja_cukup_untuk_akses_admin(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik();
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        Penugasan::factory()->wakilKepalaSekolah()->for($tenagaPendidik)->for($aktif)->create();

        $this->assertTrue(Gate::forUser($pengguna)->allows('akses-admin'));
    }

    public function test_dasbor_menampilkan_peran_dari_penugasan(): void
    {
        [$pengguna, $tenagaPendidik] = $this->akunTenagaPendidik(KodePeran::TenagaPendidik);
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        Penugasan::factory()->waliKelas()->for($tenagaPendidik)->for($aktif)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.dasbor'))
            ->assertOk()
            ->assertSee('Peran Anda: Wali Kelas, Tenaga Pendidik.');
    }
}
