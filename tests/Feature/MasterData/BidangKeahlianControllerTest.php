<?php

namespace Tests\Feature\MasterData;

use App\Enums\KodePeran;
use App\Models\BidangKeahlian;
use App\Models\Pengguna;
use App\Models\ProgramKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BidangKeahlianControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_ketua_jurusan_tidak_boleh_membuka_daftar(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::KetuaJurusan)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.master-data.bidang-keahlian.index'))
            ->assertForbidden();
    }

    public function test_administrator_melihat_daftar_beserta_jumlah_program(): void
    {
        $bidang = BidangKeahlian::factory()->has(ProgramKeahlian::factory()->count(2))->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.bidang-keahlian.index'))
            ->assertOk()
            ->assertSeeInOrder([$bidang->nama, '2']);
    }

    public function test_administrator_menambah_bidang_keahlian(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.bidang-keahlian.store'), ['nama' => 'Pariwisata'])
            ->assertRedirect(route('admin.master-data.bidang-keahlian.index'));

        $this->assertDatabaseHas('tb_bidang_keahlian', ['nama' => 'Pariwisata']);
    }

    public function test_nama_bidang_keahlian_tidak_boleh_ganda(): void
    {
        BidangKeahlian::factory()->create(['nama' => 'Pariwisata']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.bidang-keahlian.store'), ['nama' => 'Pariwisata'])
            ->assertSessionHasErrors(['nama' => 'Nama bidang keahlian sudah digunakan.']);
    }

    public function test_administrator_mengubah_bidang_keahlian(): void
    {
        $bidang = BidangKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.bidang-keahlian.update', $bidang), ['nama' => 'Bisnis dan Manajemen'])
            ->assertRedirect(route('admin.master-data.bidang-keahlian.index'));

        $this->assertSame('Bisnis dan Manajemen', $bidang->refresh()->nama);
    }

    public function test_bidang_keahlian_tanpa_program_dapat_dihapus(): void
    {
        $bidang = BidangKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.bidang-keahlian.destroy', $bidang))
            ->assertRedirect(route('admin.master-data.bidang-keahlian.index'));

        $this->assertSoftDeleted($bidang);
    }

    public function test_bidang_keahlian_yang_masih_memiliki_program_tidak_dapat_dihapus(): void
    {
        $bidang = BidangKeahlian::factory()->has(ProgramKeahlian::factory())->create(['nama' => 'Pariwisata']);

        $this->actingAs($this->admin())
            ->from(route('admin.master-data.bidang-keahlian.index'))
            ->delete(route('admin.master-data.bidang-keahlian.destroy', $bidang))
            ->assertRedirect(route('admin.master-data.bidang-keahlian.index'))
            ->assertSessionHas('galat', 'Bidang keahlian Pariwisata masih memiliki program keahlian.');

        $this->assertNotSoftDeleted($bidang);
    }
}
