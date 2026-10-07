<?php

namespace Tests\Feature\MasterData;

use App\Enums\HubunganOrangTua;
use App\Enums\KodePeran;
use App\Models\Murid;
use App\Models\OrangTuaWali;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuridOrangTuaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_tenaga_kependidikan_tidak_boleh_menautkan_orang_tua(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaKependidikan)->create();
        $murid = Murid::factory()->create();
        $orangTua = OrangTuaWali::factory()->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.murid.orang-tua-wali.store', $murid), [
                'id_orang_tua_wali' => $orangTua->id_orang_tua_wali,
                'hubungan' => HubunganOrangTua::AyahKandung->value,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('tb_murid_orang_tua', 0);
    }

    public function test_murid_bersaudara_memakai_data_orang_tua_yang_sama(): void
    {
        $ayah = OrangTuaWali::factory()->create();
        $kakak = Murid::factory()->create();
        $adik = Murid::factory()->create();
        $kakak->orangTuaWali()->attach($ayah, ['hubungan' => HubunganOrangTua::AyahKandung->value]);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.orang-tua-wali.store', $adik), [
                'id_orang_tua_wali' => $ayah->id_orang_tua_wali,
                'hubungan' => HubunganOrangTua::AyahKandung->value,
            ])
            ->assertRedirect(route('admin.master-data.murid.edit', $adik))
            ->assertSessionHasNoErrors();

        $this->assertCount(2, $ayah->murid);
    }

    public function test_hubungan_yang_sama_tidak_boleh_dua_kali(): void
    {
        $murid = Murid::factory()->create();
        $murid->orangTuaWali()->attach(OrangTuaWali::factory()->create(), ['hubungan' => HubunganOrangTua::AyahKandung->value]);
        $lain = OrangTuaWali::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.orang-tua-wali.store', $murid), [
                'id_orang_tua_wali' => $lain->id_orang_tua_wali,
                'hubungan' => HubunganOrangTua::AyahKandung->value,
            ])
            ->assertSessionHasErrors(['hubungan' => 'Murid ini sudah memiliki ayah kandung.']);
    }

    public function test_orang_tua_yang_sama_tidak_boleh_ditautkan_dua_kali_ke_murid_yang_sama(): void
    {
        $murid = Murid::factory()->create();
        $orangTua = OrangTuaWali::factory()->create();
        $murid->orangTuaWali()->attach($orangTua, ['hubungan' => HubunganOrangTua::IbuKandung->value]);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.orang-tua-wali.store', $murid), [
                'id_orang_tua_wali' => $orangTua->id_orang_tua_wali,
                'hubungan' => HubunganOrangTua::Wali->value,
            ])
            ->assertSessionHasErrors(['id_orang_tua_wali' => 'Orang tua/wali ini sudah tertaut ke murid tersebut.']);
    }

    public function test_halaman_ubah_murid_menampilkan_orang_tua_dan_hubungan_yang_tersisa(): void
    {
        $murid = Murid::factory()->create();
        $murid->orangTuaWali()->attach(OrangTuaWali::factory()->create(['nama' => 'Hendra Gunawan']), [
            'hubungan' => HubunganOrangTua::AyahKandung->value,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.murid.edit', $murid))
            ->assertOk()
            ->assertSeeInOrder(['Ayah kandung', 'Hendra Gunawan'])
            ->assertSee('Tambah ibu kandung baru')
            ->assertDontSee('Tambah ayah kandung baru');
    }

    public function test_administrator_melepas_tautan_orang_tua(): void
    {
        $murid = Murid::factory()->create();
        $orangTua = OrangTuaWali::factory()->create();
        $murid->orangTuaWali()->attach($orangTua, ['hubungan' => HubunganOrangTua::Wali->value]);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.murid.orang-tua-wali.destroy', [$murid, $orangTua]))
            ->assertRedirect(route('admin.master-data.murid.edit', $murid));

        $this->assertDatabaseCount('tb_murid_orang_tua', 0);
        $this->assertNotSoftDeleted($orangTua);
    }
}
