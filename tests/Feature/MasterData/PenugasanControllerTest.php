<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\KonsentrasiKeahlian;
use App\Models\Pengguna;
use App\Models\Penugasan;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenugasanControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_wali_kelas_tidak_boleh_menambah_penugasan(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::WaliKelas)->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::KepalaSekolah->value,
                'id_satuan_pendidikan' => $tenagaPendidik->id_satuan_pendidikan,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('tb_penugasan', 0);
    }

    public function test_daftar_bawaan_menampilkan_penugasan_tahun_ajaran_aktif(): void
    {
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        $lama = TahunAjaran::factory()->create();
        $kepalaAktif = Penugasan::factory()->for($aktif)->create();
        $kepalaLama = Penugasan::factory()->for($lama)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.penugasan.index'))
            ->assertOk()
            ->assertSee($kepalaAktif->tenagaPendidik->nama_lengkap)
            ->assertSee('Kepala Sekolah')
            ->assertDontSee($kepalaLama->tenagaPendidik->nama_lengkap);
    }

    public function test_form_tambah_tanpa_tahun_ajaran_dialihkan_dengan_pesan(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.master-data.penugasan.create'))
            ->assertRedirect(route('admin.master-data.penugasan.index'))
            ->assertSessionHas('galat', 'Pilih tahun ajaran terlebih dahulu.');
    }

    public function test_form_tambah_hanya_menawarkan_rombel_tahun_ajaran_terpilih(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        Rombel::factory()->for($tahunAjaran)->create(['nama' => 'VII Terpilih']);
        Rombel::factory()->create(['nama' => 'VII Lain']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.penugasan.create', ['tahun_ajaran' => $tahunAjaran->id_tahun_ajaran]))
            ->assertOk()
            ->assertSee('VII Terpilih')
            ->assertDontSee('VII Lain');
    }

    public function test_administrator_menugaskan_wali_kelas_dan_mengabaikan_isian_tidak_relevan(): void
    {
        $rombel = Rombel::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();
        $satuanPendidikan = SatuanPendidikan::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $rombel->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::WaliKelas->value,
                'id_rombel' => $rombel->id_rombel,
                'id_satuan_pendidikan' => $satuanPendidikan->id_satuan_pendidikan,
                'bidang' => 'Kurikulum',
            ])
            ->assertRedirect(route('admin.master-data.penugasan.index', ['tahun_ajaran' => $rombel->id_tahun_ajaran]))
            ->assertSessionHasNoErrors();

        $penugasan = Penugasan::query()->with('peran')->sole();
        $this->assertSame(KodePeran::WaliKelas, $penugasan->peran->kode);
        $this->assertSame($rombel->id_rombel, $penugasan->id_rombel);
        $this->assertNull($penugasan->id_satuan_pendidikan);
        $this->assertNull($penugasan->bidang);
    }

    public function test_rombel_harus_berada_di_tahun_ajaran_yang_sama(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $rombelLain = Rombel::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::WaliKelas->value,
                'id_rombel' => $rombelLain->id_rombel,
            ])
            ->assertSessionHasErrors(['id_rombel' => 'Rombel harus berada pada tahun ajaran yang dipilih.']);
    }

    public function test_satu_rombel_hanya_memiliki_satu_wali_kelas(): void
    {
        $rombel = Rombel::factory()->create();
        Penugasan::factory()->waliKelas($rombel)->create(['id_tahun_ajaran' => $rombel->id_tahun_ajaran]);
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $rombel->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::WaliKelas->value,
                'id_rombel' => $rombel->id_rombel,
            ])
            ->assertSessionHasErrors(['id_rombel' => 'Rombel ini sudah memiliki wali kelas pada tahun ajaran tersebut.']);
    }

    public function test_satu_konsentrasi_hanya_memiliki_satu_ketua_jurusan_per_tahun_ajaran(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $konsentrasi = KonsentrasiKeahlian::factory()->create();
        Penugasan::factory()->ketuaJurusan($konsentrasi)->for($tahunAjaran)->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::KetuaJurusan->value,
                'id_konsentrasi_keahlian' => $konsentrasi->id_konsentrasi_keahlian,
            ])
            ->assertSessionHasErrors(['id_konsentrasi_keahlian' => 'Konsentrasi keahlian ini sudah memiliki ketua jurusan pada tahun ajaran tersebut.']);
    }

    public function test_kepala_sekolah_yang_sama_boleh_ditugaskan_lagi_di_tahun_ajaran_berikutnya(): void
    {
        $kepala = Penugasan::factory()->create();
        $tahunBerikutnya = TahunAjaran::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunBerikutnya->id_tahun_ajaran,
                'id_tenaga_pendidik' => $kepala->id_tenaga_pendidik,
                'kode_peran' => KodePeran::KepalaSekolah->value,
                'id_satuan_pendidikan' => $kepala->id_satuan_pendidikan,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tb_penugasan', 2);
    }

    public function test_satu_satuan_pendidikan_hanya_memiliki_satu_kepala_sekolah_per_tahun_ajaran(): void
    {
        $kepala = Penugasan::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $kepala->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::KepalaSekolah->value,
                'id_satuan_pendidikan' => $kepala->id_satuan_pendidikan,
            ])
            ->assertSessionHasErrors(['id_satuan_pendidikan' => 'Satuan pendidikan ini sudah memiliki kepala sekolah pada tahun ajaran tersebut.']);
    }

    public function test_wakil_kepala_sekolah_wajib_memiliki_bidang(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::WakilKepalaSekolah->value,
                'id_satuan_pendidikan' => $tenagaPendidik->id_satuan_pendidikan,
                'bidang' => '',
            ])
            ->assertSessionHasErrors(['bidang' => 'Bidang wajib diisi.']);
    }

    public function test_satuan_pendidikan_boleh_memiliki_beberapa_wakil_kepala_sekolah(): void
    {
        $wakil = Penugasan::factory()->wakilKepalaSekolah('Kurikulum')->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $wakil->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::WakilKepalaSekolah->value,
                'id_satuan_pendidikan' => $wakil->id_satuan_pendidikan,
                'bidang' => 'Kesiswaan',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_penugasan', ['bidang' => 'Kesiswaan', 'id_satuan_pendidikan' => $wakil->id_satuan_pendidikan]);
    }

    public function test_peran_tetap_tidak_dapat_dijadikan_penugasan(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.penugasan.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::Administrator->value,
            ])
            ->assertSessionHasErrors(['kode_peran' => 'Peran yang dipilih tidak valid.']);
    }

    public function test_mengubah_penugasan_tidak_bentrok_dengan_dirinya_sendiri(): void
    {
        $kepala = Penugasan::factory()->create();
        $penggantiTenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.penugasan.update', $kepala), [
                'id_tahun_ajaran' => $kepala->id_tahun_ajaran,
                'id_tenaga_pendidik' => $penggantiTenagaPendidik->id_tenaga_pendidik,
                'kode_peran' => KodePeran::KepalaSekolah->value,
                'id_satuan_pendidikan' => $kepala->id_satuan_pendidikan,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame($penggantiTenagaPendidik->id_tenaga_pendidik, $kepala->refresh()->id_tenaga_pendidik);
    }

    public function test_form_ubah_memilih_peran_penugasan(): void
    {
        $penugasan = Penugasan::factory()->ketuaJurusan()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.penugasan.edit', $penugasan))
            ->assertOk()
            ->assertSee('<option value="ketua_jurusan" selected>', false);
    }

    public function test_administrator_menghapus_penugasan(): void
    {
        $penugasan = Penugasan::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.penugasan.destroy', $penugasan))
            ->assertRedirect(route('admin.master-data.penugasan.index', ['tahun_ajaran' => $penugasan->id_tahun_ajaran]));

        $this->assertSoftDeleted($penugasan);
    }

    public function test_tenaga_pendidik_yang_memiliki_penugasan_tidak_dapat_dihapus(): void
    {
        $penugasan = Penugasan::factory()->create();
        $tenagaPendidik = $penugasan->tenagaPendidik;

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tenaga-pendidik.destroy', $tenagaPendidik))
            ->assertSessionHas('galat', "{$tenagaPendidik->nama_lengkap} masih memiliki penugasan. Hapus penugasannya terlebih dahulu.");

        $this->assertNotSoftDeleted($tenagaPendidik);
    }

    public function test_rombel_yang_memiliki_wali_kelas_tidak_dapat_dihapus(): void
    {
        $penugasan = Penugasan::factory()->waliKelas()->create();
        $rombel = $penugasan->rombel;

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.rombel.destroy', $rombel))
            ->assertSessionHas('galat', "Rombel {$rombel->nama} masih memiliki wali kelas. Hapus penugasannya terlebih dahulu.");

        $this->assertNotSoftDeleted($rombel);
    }

    public function test_tahun_ajaran_yang_memiliki_penugasan_tidak_dapat_dihapus(): void
    {
        $penugasan = Penugasan::factory()->create();
        $tahunAjaran = $penugasan->tahunAjaran;

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tahun-ajaran.destroy', $tahunAjaran))
            ->assertSessionHas('galat', "Tahun ajaran {$tahunAjaran->nama} masih memiliki penugasan.");

        $this->assertNotSoftDeleted($tahunAjaran);
    }
}
