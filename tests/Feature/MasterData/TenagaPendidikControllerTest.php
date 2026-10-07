<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenjangPendidikan;
use App\Enums\KodePeran;
use App\Enums\StatusTenagaPendidik;
use App\Models\Pengguna;
use App\Models\SatuanPendidikan;
use App\Models\TenagaPendidik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenagaPendidikControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function isianValid(SatuanPendidikan $satuanPendidikan, array $timpa = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Siti Aminah, S.Pd.',
            'nuptk' => '1234567890123456',
            'tempat_lahir' => 'Tangerang',
            'tanggal_lahir' => '1990-05-17',
            'pendidikan_terakhir' => JenjangPendidikan::S1->value,
            'id_satuan_pendidikan' => $satuanPendidikan->id_satuan_pendidikan,
            'status' => StatusTenagaPendidik::Gty->value,
            'tmt_gtt' => '2015-07-01',
            'tmt_gty' => '2020-07-01',
            'masa_kerja_tahun' => 11,
            'masa_kerja_bulan' => 3,
        ], $timpa);
    }

    public function test_tenaga_kependidikan_tidak_boleh_membuka_daftar(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaKependidikan)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.master-data.tenaga-pendidik.index'))
            ->assertForbidden();
    }

    public function test_tenaga_pendidik_tidak_boleh_mengubah_data(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($pengguna)
            ->put(route('admin.master-data.tenaga-pendidik.update', $tenagaPendidik),
                $this->isianValid($tenagaPendidik->satuanPendidikan, ['nama_lengkap' => 'Diubah']))
            ->assertForbidden();

        $this->assertNotSame('Diubah', $tenagaPendidik->refresh()->nama_lengkap);
    }

    public function test_administrator_melihat_daftar_tenaga_pendidik(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->gty()->create([
            'nama_lengkap' => 'Budi Santoso',
            'masa_kerja_tahun' => 5,
            'masa_kerja_bulan' => 2,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tenaga-pendidik.index'))
            ->assertOk()
            ->assertSeeInOrder(['Budi Santoso', $tenagaPendidik->nuptk, $tenagaPendidik->satuanPendidikan->nama, 'GTY', 'S1', '5 tahun 2 bulan']);
    }

    public function test_pencarian_berdasarkan_nama_atau_nuptk(): void
    {
        TenagaPendidik::factory()->create(['nama_lengkap' => 'Budi Santoso', 'nuptk' => '1111222233334444']);
        TenagaPendidik::factory()->create(['nama_lengkap' => 'Rina Wati', 'nuptk' => '5555666677778888']);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.master-data.tenaga-pendidik.index', ['cari' => 'budi']))
            ->assertSee('Budi Santoso')
            ->assertDontSee('Rina Wati');

        $this->actingAs($admin)
            ->get(route('admin.master-data.tenaga-pendidik.index', ['cari' => '5555']))
            ->assertSee('Rina Wati')
            ->assertDontSee('Budi Santoso');
    }

    public function test_saringan_status_dan_satuan_pendidikan(): void
    {
        $smp = SatuanPendidikan::factory()->smp()->create();
        TenagaPendidik::factory()->for($smp)->gty()->create(['nama_lengkap' => 'Guru SMP Tetap']);
        TenagaPendidik::factory()->for($smp)->create(['nama_lengkap' => 'Guru SMP Tidak Tetap']);
        TenagaPendidik::factory()->gty()->create(['nama_lengkap' => 'Guru Lain Tetap']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tenaga-pendidik.index', [
                'satuan_pendidikan' => $smp->id_satuan_pendidikan,
                'status' => StatusTenagaPendidik::Gty->value,
            ]))
            ->assertSee('Guru SMP Tetap')
            ->assertDontSee('Guru SMP Tidak Tetap')
            ->assertDontSee('Guru Lain Tetap');
    }

    public function test_administrator_menambah_tenaga_pendidik(): void
    {
        $smk = SatuanPendidikan::factory()->smk()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($smk))
            ->assertRedirect(route('admin.master-data.tenaga-pendidik.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_tenaga_pendidik', [
            'id_satuan_pendidikan' => $smk->id_satuan_pendidikan,
            'id_pengguna' => null,
            'nama_lengkap' => 'Siti Aminah, S.Pd.',
            'nuptk' => '1234567890123456',
            'pendidikan_terakhir' => 's1',
            'status' => 'gty',
            'tmt_gty' => '2020-07-01',
            'masa_kerja_tahun' => 11,
            'masa_kerja_bulan' => 3,
        ]);
    }

    public function test_nuptk_boleh_kosong(): void
    {
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($smp, ['nuptk' => '']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_tenaga_pendidik', ['nama_lengkap' => 'Siti Aminah, S.Pd.', 'nuptk' => null]);
    }

    public function test_isian_wajib_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), [])
            ->assertSessionHasErrors([
                'nama_lengkap' => 'Nama lengkap wajib diisi.',
                'pendidikan_terakhir' => 'Pendidikan terakhir wajib diisi.',
                'id_satuan_pendidikan' => 'Satuan pendidikan wajib diisi.',
                'status' => 'Status wajib diisi.',
                'masa_kerja_tahun' => 'Masa kerja (tahun) wajib diisi.',
                'masa_kerja_bulan' => 'Masa kerja (bulan) wajib diisi.',
            ]);
    }

    public function test_nuptk_harus_16_digit(): void
    {
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($smp, ['nuptk' => '12345678901234AB']))
            ->assertSessionHasErrors(['nuptk' => 'NUPTK harus terdiri dari 16 digit.']);
    }

    public function test_nuptk_tidak_boleh_sama_dengan_tenaga_pendidik_lain(): void
    {
        $lain = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($lain->satuanPendidikan, ['nuptk' => $lain->nuptk]))
            ->assertSessionHasErrors(['nuptk' => 'NUPTK sudah digunakan.']);
    }

    public function test_status_di_luar_gtt_dan_gty_ditolak(): void
    {
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($smp, ['status' => 'honorer']))
            ->assertSessionHasErrors(['status' => 'Status yang dipilih tidak valid.']);
    }

    public function test_masa_kerja_bulan_maksimal_11(): void
    {
        $smp = SatuanPendidikan::factory()->smp()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-pendidik.store'), $this->isianValid($smp, ['masa_kerja_bulan' => 12]))
            ->assertSessionHasErrors(['masa_kerja_bulan' => 'Masa kerja (bulan) tidak boleh lebih besar dari 11.']);
    }

    public function test_administrator_mengubah_tenaga_pendidik_tanpa_mengganti_nuptk(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.tenaga-pendidik.update', $tenagaPendidik),
                $this->isianValid($tenagaPendidik->satuanPendidikan, ['nuptk' => $tenagaPendidik->nuptk, 'nama_lengkap' => 'Nama Baru']))
            ->assertRedirect(route('admin.master-data.tenaga-pendidik.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Baru', $tenagaPendidik->refresh()->nama_lengkap);
    }

    public function test_form_ubah_terisi_data_tenaga_pendidik(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create(['tanggal_lahir' => '1990-05-17']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tenaga-pendidik.edit', $tenagaPendidik))
            ->assertOk()
            ->assertSee($tenagaPendidik->nama_lengkap)
            ->assertSee('value="1990-05-17"', false);
    }

    public function test_administrator_menghapus_tenaga_pendidik(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tenaga-pendidik.destroy', $tenagaPendidik))
            ->assertRedirect(route('admin.master-data.tenaga-pendidik.index'));

        $this->assertSoftDeleted($tenagaPendidik);
    }

    public function test_satuan_pendidikan_yang_memiliki_tenaga_pendidik_tidak_dapat_dihapus(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create();
        $satuanPendidikan = $tenagaPendidik->satuanPendidikan;

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.satuan-pendidikan.destroy', $satuanPendidikan))
            ->assertSessionHas('galat', "Satuan pendidikan {$satuanPendidikan->nama} masih memiliki tenaga pendidik.");

        $this->assertNotSoftDeleted($satuanPendidikan);
    }
}
