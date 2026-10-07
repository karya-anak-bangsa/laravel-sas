<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Enums\Tingkat;
use App\Models\KonsentrasiKeahlian;
use App\Models\Pengguna;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RombelControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_tenaga_pendidik_tidak_boleh_menambah_rombel(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smp->id_satuan_pendidikan,
                'tingkat' => Tingkat::VII->value,
                'nama' => 'VII',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('tb_rombel', 0);
    }

    public function test_daftar_bawaan_hanya_menampilkan_rombel_tahun_ajaran_aktif(): void
    {
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        $lama = TahunAjaran::factory()->create();
        Rombel::factory()->for($aktif)->create(['nama' => 'VII Aktif']);
        Rombel::factory()->for($lama)->create(['nama' => 'VII Lama']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.index'))
            ->assertOk()
            ->assertSee('VII Aktif')
            ->assertDontSee('VII Lama');
    }

    public function test_saringan_kosong_menampilkan_semua_tahun_ajaran(): void
    {
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        $lama = TahunAjaran::factory()->create();
        Rombel::factory()->for($aktif)->create(['nama' => 'VII Aktif']);
        Rombel::factory()->for($lama)->create(['nama' => 'VII Lama']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.index', ['tahun_ajaran' => '']))
            ->assertOk()
            ->assertSee('VII Aktif')
            ->assertSee('VII Lama');
    }

    public function test_daftar_dapat_disaring_per_satuan_pendidikan(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $rombelSmp = Rombel::factory()->for($tahunAjaran)->create(['nama' => 'VII Puspita']);
        Rombel::factory()->smk()->for($tahunAjaran)->create(['nama' => 'X RPL']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.index', [
                'tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'satuan_pendidikan' => $rombelSmp->id_satuan_pendidikan,
            ]))
            ->assertOk()
            ->assertSee('VII Puspita')
            ->assertDontSee('X RPL');
    }

    public function test_form_tambah_memilih_tahun_ajaran_aktif_secara_bawaan(): void
    {
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Genap)->create();
        TahunAjaran::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.create'))
            ->assertOk()
            ->assertSee('<option value="'.$aktif->id_tahun_ajaran.'" selected>', false);
    }

    public function test_administrator_menambah_rombel_smp(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smp->id_satuan_pendidikan,
                'tingkat' => Tingkat::VIII->value,
                'nama' => 'VIII',
            ])
            ->assertRedirect(route('admin.master-data.rombel.index', ['tahun_ajaran' => $tahunAjaran->id_tahun_ajaran]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_rombel', [
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
            'id_satuan_pendidikan' => $smp->id_satuan_pendidikan,
            'id_konsentrasi_keahlian' => null,
            'tingkat' => 8,
            'nama' => 'VIII',
        ]);
    }

    public function test_administrator_menambah_rombel_smk_dengan_konsentrasi(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $smk = SatuanPendidikan::factory()->smk()->create();
        $perhotelan = KonsentrasiKeahlian::factory()->create(['singkatan' => 'PH']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smk->id_satuan_pendidikan,
                'id_konsentrasi_keahlian' => $perhotelan->id_konsentrasi_keahlian,
                'tingkat' => Tingkat::X->value,
                'nama' => 'X PH 2',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_rombel', [
            'id_konsentrasi_keahlian' => $perhotelan->id_konsentrasi_keahlian,
            'tingkat' => 10,
            'nama' => 'X PH 2',
        ]);
    }

    public function test_tingkat_harus_sesuai_bentuk_pendidikan(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smp->id_satuan_pendidikan,
                'tingkat' => Tingkat::X->value,
                'nama' => 'X',
            ])
            ->assertSessionHasErrors(['tingkat' => 'Tingkat X tidak tersedia di SMP.']);
    }

    public function test_rombel_smk_wajib_memiliki_konsentrasi_keahlian(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $smk = SatuanPendidikan::factory()->smk()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smk->id_satuan_pendidikan,
                'tingkat' => Tingkat::X->value,
                'nama' => 'X RPL',
            ])
            ->assertSessionHasErrors(['id_konsentrasi_keahlian' => 'Konsentrasi keahlian wajib diisi untuk rombel SMK.']);
    }

    public function test_rombel_smp_tidak_boleh_memiliki_konsentrasi_keahlian(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $smp = SatuanPendidikan::factory()->smp()->create();
        $konsentrasi = KonsentrasiKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $smp->id_satuan_pendidikan,
                'id_konsentrasi_keahlian' => $konsentrasi->id_konsentrasi_keahlian,
                'tingkat' => Tingkat::VII->value,
                'nama' => 'VII',
            ])
            ->assertSessionHasErrors(['id_konsentrasi_keahlian' => 'Rombel SMP tidak memiliki konsentrasi keahlian.']);
    }

    public function test_isian_wajib_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [])
            ->assertSessionHasErrors([
                'id_tahun_ajaran' => 'Tahun ajaran wajib diisi.',
                'id_satuan_pendidikan' => 'Satuan pendidikan wajib diisi.',
                'tingkat' => 'Tingkat wajib diisi.',
                'nama' => 'Nama rombel wajib diisi.',
            ]);
    }

    public function test_nama_rombel_unik_per_satuan_pendidikan_dan_tahun_ajaran(): void
    {
        $rombel = Rombel::factory()->create(['nama' => 'VII']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $rombel->id_tahun_ajaran,
                'id_satuan_pendidikan' => $rombel->id_satuan_pendidikan,
                'tingkat' => Tingkat::VII->value,
                'nama' => 'VII',
            ])
            ->assertSessionHasErrors(['nama' => 'Nama rombel sudah dipakai di satuan pendidikan dan tahun ajaran yang sama.']);
    }

    public function test_nama_rombel_yang_sama_boleh_dipakai_di_tahun_ajaran_lain(): void
    {
        $rombel = Rombel::factory()->create(['nama' => 'VII']);
        $tahunAjaranLain = TahunAjaran::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.store'), [
                'id_tahun_ajaran' => $tahunAjaranLain->id_tahun_ajaran,
                'id_satuan_pendidikan' => $rombel->id_satuan_pendidikan,
                'tingkat' => Tingkat::VII->value,
                'nama' => 'VII',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_administrator_mengubah_rombel(): void
    {
        $rombel = Rombel::factory()->create(['nama' => 'VII']);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.rombel.update', $rombel), [
                'id_tahun_ajaran' => $rombel->id_tahun_ajaran,
                'id_satuan_pendidikan' => $rombel->id_satuan_pendidikan,
                'tingkat' => Tingkat::IX->value,
                'nama' => 'IX',
            ])
            ->assertSessionHasNoErrors();

        $rombel->refresh();
        $this->assertSame(Tingkat::IX, $rombel->tingkat);
        $this->assertSame('IX', $rombel->nama);
    }

    public function test_administrator_menghapus_rombel(): void
    {
        $rombel = Rombel::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.rombel.destroy', $rombel))
            ->assertRedirect(route('admin.master-data.rombel.index', ['tahun_ajaran' => $rombel->id_tahun_ajaran]));

        $this->assertSoftDeleted($rombel);
    }

    public function test_satuan_pendidikan_yang_memiliki_rombel_tidak_dapat_dihapus(): void
    {
        $rombel = Rombel::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.satuan-pendidikan.destroy', $rombel->satuanPendidikan))
            ->assertSessionHas('galat', "Satuan pendidikan {$rombel->satuanPendidikan->nama} masih memiliki rombel.");

        $this->assertNotSoftDeleted($rombel->satuanPendidikan);
    }

    public function test_tahun_ajaran_yang_memiliki_rombel_tidak_dapat_dihapus(): void
    {
        $rombel = Rombel::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tahun-ajaran.destroy', $rombel->tahunAjaran))
            ->assertSessionHas('galat', "Tahun ajaran {$rombel->tahunAjaran->nama} masih memiliki rombel.");

        $this->assertNotSoftDeleted($rombel->tahunAjaran);
    }

    public function test_konsentrasi_keahlian_yang_dipakai_rombel_tidak_dapat_dihapus(): void
    {
        $rombel = Rombel::factory()->smk()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.konsentrasi-keahlian.destroy', $rombel->konsentrasiKeahlian))
            ->assertSessionHas('galat', "Konsentrasi keahlian {$rombel->konsentrasiKeahlian->nama} masih dipakai oleh rombel.");

        $this->assertNotSoftDeleted($rombel->konsentrasiKeahlian);
    }
}
