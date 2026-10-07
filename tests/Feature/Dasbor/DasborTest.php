<?php

namespace Tests\Feature\Dasbor;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DasborTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get(route('admin.dasbor'))->assertRedirect(route('admin.masuk'));
    }

    public function test_pengguna_dengan_peran_dapat_membuka_dasbor(): void
    {
        $pengguna = Pengguna::factory()
            ->denganPeran(KodePeran::TenagaPendidik, KodePeran::WaliKelas)
            ->create();

        $this->actingAs($pengguna)
            ->get(route('admin.dasbor'))
            ->assertOk()
            ->assertViewIs('admin.dasbor.index')
            ->assertSee($pengguna->nama_pengguna)
            ->assertSee('Wali Kelas, Tenaga Pendidik');
    }

    public function test_dasbor_menampilkan_semester_aktif(): void
    {
        TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create(['nama' => '2026/2027']);
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.dasbor'))
            ->assertOk()
            ->assertSee('Ganjil 2026/2027')
            ->assertSee('1 Juli 2026');
    }

    public function test_dasbor_memberi_tahu_jika_belum_ada_semester_aktif(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.dasbor'))
            ->assertOk()
            ->assertSee('Belum ada semester aktif.')
            ->assertDontSee('Atur di Tahun Ajaran');
    }

    public function test_pengguna_tanpa_peran_ditolak(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->actingAs($pengguna)->get(route('admin.dasbor'))->assertForbidden();
    }

    public function test_akun_yang_dinonaktifkan_saat_masih_masuk_ditolak(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
        $pengguna->update(['aktif' => false]);

        $this->actingAs($pengguna)->get(route('admin.dasbor'))->assertForbidden();
    }
}
