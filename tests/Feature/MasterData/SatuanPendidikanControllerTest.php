<?php

namespace Tests\Feature\MasterData;

use App\Enums\BentukPendidikan;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\SatuanPendidikan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatuanPendidikanControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get(route('admin.master-data.satuan-pendidikan.index'))
            ->assertRedirect(route('admin.masuk'));
    }

    public function test_tenaga_pendidik_tidak_boleh_membuka_daftar(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.master-data.satuan-pendidikan.index'))
            ->assertForbidden();
    }

    public function test_tenaga_pendidik_tidak_boleh_menambah(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMP Uji',
                'bentuk_pendidikan' => BentukPendidikan::Smp->value,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('tb_satuan_pendidikan', ['nama' => 'SMP Uji']);
    }

    public function test_administrator_melihat_daftar_satuan_pendidikan(): void
    {
        $satuanPendidikan = SatuanPendidikan::factory()->smk()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.satuan-pendidikan.index'))
            ->assertOk()
            ->assertSee($satuanPendidikan->nama)
            ->assertSee('SMK');
    }

    public function test_administrator_membuka_form_tambah_dan_ubah(): void
    {
        $admin = $this->admin();
        $satuanPendidikan = SatuanPendidikan::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.master-data.satuan-pendidikan.create'))
            ->assertOk()
            ->assertSee('Tambah Satuan Pendidikan');

        $this->actingAs($admin)
            ->get(route('admin.master-data.satuan-pendidikan.edit', $satuanPendidikan))
            ->assertOk()
            ->assertSee($satuanPendidikan->nama);
    }

    public function test_administrator_menambah_satuan_pendidikan(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMP Puspita Bangsa',
                'bentuk_pendidikan' => BentukPendidikan::Smp->value,
                'npsn' => '20123456',
                'alamat' => 'Ciputat',
            ])
            ->assertRedirect(route('admin.master-data.satuan-pendidikan.index'))
            ->assertSessionHas('status', 'Satuan pendidikan berhasil ditambahkan.');

        $this->assertDatabaseHas('tb_satuan_pendidikan', [
            'nama' => 'SMP Puspita Bangsa',
            'bentuk_pendidikan' => 'smp',
            'npsn' => '20123456',
            'alamat' => 'Ciputat',
        ]);
    }

    public function test_isian_wajib_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [])
            ->assertSessionHasErrors([
                'nama' => 'Nama satuan pendidikan wajib diisi.',
                'bentuk_pendidikan' => 'Bentuk pendidikan wajib diisi.',
            ]);
    }

    public function test_bentuk_pendidikan_di_luar_pilihan_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMA Uji',
                'bentuk_pendidikan' => 'sma',
            ])
            ->assertSessionHasErrors(['bentuk_pendidikan' => 'Bentuk pendidikan yang dipilih tidak valid.']);
    }

    public function test_npsn_harus_8_digit(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMP Uji',
                'bentuk_pendidikan' => BentukPendidikan::Smp->value,
                'npsn' => '12345',
            ])
            ->assertSessionHasErrors(['npsn' => 'NPSN harus terdiri dari 8 digit.']);
    }

    public function test_npsn_tidak_boleh_sama_dengan_satuan_pendidikan_lain(): void
    {
        SatuanPendidikan::factory()->create(['npsn' => '20123456']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMP Uji',
                'bentuk_pendidikan' => BentukPendidikan::Smp->value,
                'npsn' => '20123456',
            ])
            ->assertSessionHasErrors(['npsn' => 'NPSN sudah digunakan.']);
    }

    public function test_npsn_satuan_pendidikan_yang_sudah_dihapus_boleh_dipakai_lagi(): void
    {
        SatuanPendidikan::factory()->create(['npsn' => '20123456'])->delete();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.satuan-pendidikan.store'), [
                'nama' => 'SMP Uji',
                'bentuk_pendidikan' => BentukPendidikan::Smp->value,
                'npsn' => '20123456',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_administrator_mengubah_satuan_pendidikan_tanpa_mengganti_npsn(): void
    {
        $satuanPendidikan = SatuanPendidikan::factory()->create(['npsn' => '20123456']);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.satuan-pendidikan.update', $satuanPendidikan), [
                'nama' => 'SMK Puspita Bangsa',
                'bentuk_pendidikan' => BentukPendidikan::Smk->value,
                'npsn' => '20123456',
            ])
            ->assertRedirect(route('admin.master-data.satuan-pendidikan.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('SMK Puspita Bangsa', $satuanPendidikan->refresh()->nama);
        $this->assertSame(BentukPendidikan::Smk, $satuanPendidikan->bentuk_pendidikan);
    }

    public function test_administrator_menghapus_satuan_pendidikan_secara_soft_delete(): void
    {
        $satuanPendidikan = SatuanPendidikan::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.satuan-pendidikan.destroy', $satuanPendidikan))
            ->assertRedirect(route('admin.master-data.satuan-pendidikan.index'));

        $this->assertSoftDeleted($satuanPendidikan);
    }
}
