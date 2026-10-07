<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunAjaranControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    /**
     * @return array<string, string>
     */
    private function isianValid(array $timpa = []): array
    {
        return array_merge([
            'nama' => '2026/2027',
            'ganjil_mulai' => '2026-07-13',
            'ganjil_selesai' => '2026-12-19',
            'genap_mulai' => '2027-01-04',
            'genap_selesai' => '2027-06-26',
        ], $timpa);
    }

    public function test_kepala_sekolah_tidak_boleh_menambah(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::KepalaSekolah)->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid())
            ->assertForbidden();

        $this->assertDatabaseCount('tb_tahun_ajaran', 0);
    }

    public function test_administrator_melihat_daftar_dengan_tanggal_dan_status_semester(): void
    {
        TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tahun-ajaran.index'))
            ->assertOk()
            ->assertSeeInOrder(['2026/2027', '1 Jul 2026', '31 Des 2026', 'Aktif', '1 Jan 2027', 'Aktifkan']);
    }

    public function test_administrator_menambah_tahun_ajaran_beserta_dua_semester(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid())
            ->assertRedirect(route('admin.master-data.tahun-ajaran.index'));

        $tahunAjaran = TahunAjaran::query()->where('nama', '2026/2027')->firstOrFail();

        $this->assertSame('2026-07-13', $tahunAjaran->semesterGanjil->tanggal_mulai->toDateString());
        $this->assertSame('2026-12-19', $tahunAjaran->semesterGanjil->tanggal_selesai->toDateString());
        $this->assertSame('2027-01-04', $tahunAjaran->semesterGenap->tanggal_mulai->toDateString());
        $this->assertSame('2027-06-26', $tahunAjaran->semesterGenap->tanggal_selesai->toDateString());
        $this->assertFalse($tahunAjaran->semesterGanjil->aktif);
        $this->assertFalse($tahunAjaran->semesterGenap->aktif);
    }

    public function test_format_nama_tahun_ajaran_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid(['nama' => '2026-2027']))
            ->assertSessionHasErrors(['nama' => 'Format tahun ajaran harus TTTT/TTTT, mis. 2026/2027.']);
    }

    public function test_tahun_kedua_harus_tepat_satu_tahun_setelah_tahun_pertama(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid(['nama' => '2026/2028']))
            ->assertSessionHasErrors([
                'nama' => 'Tahun kedua pada tahun ajaran harus satu tahun setelah tahun pertama, mis. 2026/2027.',
            ]);
    }

    public function test_nama_tahun_ajaran_tidak_boleh_ganda(): void
    {
        TahunAjaran::factory()->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid())
            ->assertSessionHasErrors(['nama' => 'Tahun ajaran sudah digunakan.']);
    }

    public function test_semester_genap_harus_dimulai_setelah_semester_ganjil_selesai(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid(['genap_mulai' => '2026-12-01']))
            ->assertSessionHasErrors([
                'genap_mulai' => 'Tanggal mulai semester genap harus berupa tanggal setelah tanggal selesai semester ganjil.',
            ]);
    }

    public function test_tanggal_selesai_harus_setelah_tanggal_mulai(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tahun-ajaran.store'), $this->isianValid(['ganjil_selesai' => '2026-07-01']))
            ->assertSessionHasErrors([
                'ganjil_selesai' => 'Tanggal selesai semester ganjil harus berupa tanggal setelah tanggal mulai semester ganjil.',
            ]);
    }

    public function test_administrator_mengubah_tahun_ajaran_dan_tanggal_semester(): void
    {
        $tahunAjaran = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.tahun-ajaran.update', $tahunAjaran), $this->isianValid(['ganjil_mulai' => '2026-07-20']))
            ->assertRedirect(route('admin.master-data.tahun-ajaran.index'))
            ->assertSessionHasNoErrors();

        $tahunAjaran->refresh();
        $this->assertCount(2, $tahunAjaran->semester);
        $this->assertSame('2026-07-20', $tahunAjaran->semesterGanjil->tanggal_mulai->toDateString());
        $this->assertTrue($tahunAjaran->semesterGanjil->aktif, 'Status aktif tidak boleh berubah karena penyuntingan.');
    }

    public function test_form_ubah_terisi_tanggal_semester(): void
    {
        $tahunAjaran = TahunAjaran::factory()->denganSemester()->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tahun-ajaran.edit', $tahunAjaran))
            ->assertOk()
            ->assertSee('value="2026-07-01"', false)
            ->assertSee('value="2027-06-30"', false);
    }

    public function test_tahun_ajaran_tidak_aktif_dapat_dihapus(): void
    {
        $tahunAjaran = TahunAjaran::factory()->denganSemester()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tahun-ajaran.destroy', $tahunAjaran))
            ->assertRedirect(route('admin.master-data.tahun-ajaran.index'));

        $this->assertSoftDeleted($tahunAjaran);
    }

    public function test_tahun_ajaran_yang_sedang_aktif_tidak_dapat_dihapus(): void
    {
        $tahunAjaran = TahunAjaran::factory()->denganSemester(JenisSemester::Genap)->create(['nama' => '2026/2027']);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tahun-ajaran.destroy', $tahunAjaran))
            ->assertSessionHas('galat', 'Tahun ajaran 2026/2027 sedang aktif dan tidak dapat dihapus.');

        $this->assertNotSoftDeleted($tahunAjaran);
    }
}
