<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterAktifControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_mengaktifkan_semester_menonaktifkan_semester_lain(): void
    {
        $lama = TahunAjaran::factory()->denganSemester(JenisSemester::Genap)->create(['nama' => '2025/2026']);
        $baru = TahunAjaran::factory()->denganSemester()->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.semester-aktif.update', $baru->semesterGanjil))
            ->assertRedirect(route('admin.master-data.tahun-ajaran.index'))
            ->assertSessionHas('status', 'Semester Ganjil 2026/2027 sekarang aktif.');

        $this->assertTrue($baru->semesterGanjil->refresh()->aktif);
        $this->assertFalse($lama->semesterGenap->refresh()->aktif);
        $this->assertSame(1, Semester::query()->aktif()->count());
    }

    public function test_wakil_kepala_sekolah_tidak_boleh_mengaktifkan_semester(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::WakilKepalaSekolah)->create();
        $tahunAjaran = TahunAjaran::factory()->denganSemester()->create();

        $this->actingAs($pengguna)
            ->put(route('admin.master-data.semester-aktif.update', $tahunAjaran->semesterGanjil))
            ->assertForbidden();

        $this->assertFalse($tahunAjaran->semesterGanjil->refresh()->aktif);
    }

    public function test_semester_dari_tahun_ajaran_yang_dihapus_tidak_ditemukan(): void
    {
        $tahunAjaran = TahunAjaran::factory()->denganSemester()->create();
        $semester = $tahunAjaran->semesterGanjil;
        $tahunAjaran->delete();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.semester-aktif.update', $semester))
            ->assertNotFound();

        $this->assertFalse($semester->refresh()->aktif);
    }
}
