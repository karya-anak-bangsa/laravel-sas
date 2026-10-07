<?php

namespace Tests\Feature\MasterData;

use App\Enums\HubunganOrangTua;
use App\Enums\KodePeran;
use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use App\Models\Murid;
use App\Models\OrangTuaWali;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangTuaWaliControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function isianValid(array $timpa = []): array
    {
        return array_merge([
            'nama' => 'Hendra Gunawan',
            'pendidikan' => PendidikanOrangTua::D4S1->value,
            'pekerjaan' => PekerjaanOrangTua::Wiraswasta->value,
            'penghasilan' => PenghasilanOrangTua::Antara5JutaDan20Juta->value,
            'nomor_hp' => '+62 812 3456 7890',
        ], $timpa);
    }

    public function test_tenaga_pendidik_tidak_boleh_membuka_data_orang_tua(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)->get(route('admin.master-data.orang-tua-wali.index'))->assertForbidden();
    }

    public function test_daftar_menampilkan_murid_yang_tertaut(): void
    {
        $orangTua = OrangTuaWali::factory()->create(['nama' => 'Hendra Gunawan']);
        $murid = Murid::factory()->create(['nama_lengkap' => 'Rizky Pratama']);
        $murid->orangTuaWali()->attach($orangTua, ['hubungan' => HubunganOrangTua::AyahKandung->value]);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.orang-tua-wali.index'))
            ->assertOk()
            ->assertSeeInOrder(['Hendra Gunawan', 'Rizky Pratama (ayah kandung)']);
    }

    public function test_administrator_menambah_orang_tua_dengan_referensi_dapodik(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.orang-tua-wali.store'), $this->isianValid())
            ->assertRedirect(route('admin.master-data.orang-tua-wali.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_orang_tua_wali', [
            'nama' => 'Hendra Gunawan',
            'pendidikan' => 'd4_s1',
            'pekerjaan' => 'wiraswasta',
            'penghasilan' => '5jt_20jt',
            'nomor_hp' => '+6281234567890',
        ]);
    }

    public function test_referensi_di_luar_dapodik_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.orang-tua-wali.store'), $this->isianValid([
                'pendidikan' => 'paket_c',
                'pekerjaan' => 'youtuber',
                'penghasilan' => 'banyak',
            ]))
            ->assertSessionHasErrors([
                'pendidikan' => 'Pendidikan yang dipilih tidak valid.',
                'pekerjaan' => 'Pekerjaan yang dipilih tidak valid.',
                'penghasilan' => 'Penghasilan yang dipilih tidak valid.',
            ]);
    }

    public function test_nama_wajib_diisi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.orang-tua-wali.store'), [])
            ->assertSessionHasErrors(['nama' => 'Nama orang tua/wali wajib diisi.']);
    }

    public function test_form_dari_halaman_murid_menampilkan_nama_murid(): void
    {
        $murid = Murid::factory()->create(['nama_lengkap' => 'Rizky Pratama']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.orang-tua-wali.create', ['murid' => $murid->id_murid, 'hubungan' => 'ibu_kandung']))
            ->assertOk()
            ->assertSee('Rizky Pratama')
            ->assertSee('<option value="ibu_kandung" selected>', false);
    }

    public function test_data_baru_dari_halaman_murid_langsung_ditautkan(): void
    {
        $murid = Murid::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.orang-tua-wali.store'), $this->isianValid([
                'id_murid' => $murid->id_murid,
                'hubungan' => HubunganOrangTua::IbuKandung->value,
                'nama' => 'Sri Wahyuni',
            ]))
            ->assertRedirect(route('admin.master-data.murid.edit', $murid))
            ->assertSessionHasNoErrors();

        $orangTua = $murid->orangTuaWali()->sole();
        $this->assertSame('Sri Wahyuni', $orangTua->nama);
        $this->assertSame(HubunganOrangTua::IbuKandung, $orangTua->pivot->hubungan);
    }

    public function test_hubungan_yang_sudah_terisi_tidak_dapat_ditambah_lagi(): void
    {
        $murid = Murid::factory()->create();
        $murid->orangTuaWali()->attach(OrangTuaWali::factory()->create(), ['hubungan' => HubunganOrangTua::IbuKandung->value]);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.orang-tua-wali.store'), $this->isianValid([
                'id_murid' => $murid->id_murid,
                'hubungan' => HubunganOrangTua::IbuKandung->value,
            ]))
            ->assertSessionHasErrors(['hubungan' => 'Murid ini sudah memiliki ibu kandung.']);

        $this->assertDatabaseCount('tb_orang_tua_wali', 1);
    }

    public function test_administrator_mengubah_data_orang_tua(): void
    {
        $orangTua = OrangTuaWali::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.orang-tua-wali.update', $orangTua), $this->isianValid(['nama' => 'Nama Baru']))
            ->assertRedirect(route('admin.master-data.orang-tua-wali.index'));

        $this->assertSame('Nama Baru', $orangTua->refresh()->nama);
    }

    public function test_orang_tua_tanpa_murid_dapat_dihapus(): void
    {
        $orangTua = OrangTuaWali::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.orang-tua-wali.destroy', $orangTua))
            ->assertRedirect(route('admin.master-data.orang-tua-wali.index'));

        $this->assertSoftDeleted($orangTua);
    }

    public function test_orang_tua_yang_masih_tertaut_ke_murid_tidak_dapat_dihapus(): void
    {
        $orangTua = OrangTuaWali::factory()->create(['nama' => 'Hendra Gunawan']);
        Murid::factory()->create()->orangTuaWali()->attach($orangTua, ['hubungan' => HubunganOrangTua::AyahKandung->value]);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.orang-tua-wali.destroy', $orangTua))
            ->assertSessionHas('galat', 'Hendra Gunawan masih tertaut ke murid. Lepaskan tautannya terlebih dahulu.');

        $this->assertNotSoftDeleted($orangTua);
    }
}
