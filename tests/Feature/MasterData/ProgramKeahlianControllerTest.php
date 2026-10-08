<?php

namespace Tests\Feature\MasterData;

use App\Enums\KodePeran;
use App\Models\BidangKeahlian;
use App\Models\KonsentrasiKeahlian;
use App\Models\Pengguna;
use App\Models\ProgramKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramKeahlianControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_tenaga_kependidikan_tidak_boleh_menambah(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaKependidikan)->create();
        $bidang = BidangKeahlian::factory()->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.program-keahlian.store'), [
                'id_bidang_keahlian' => $bidang->id_bidang_keahlian,
                'nama' => 'Perhotelan',
            ])
            ->assertForbidden();
    }

    public function test_administrator_melihat_daftar_dengan_nama_bidang(): void
    {
        $program = ProgramKeahlian::factory()->create();
        KonsentrasiKeahlian::factory()->for($program)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.program-keahlian.index'))
            ->assertOk()
            ->assertSeeInOrder(['<th>Bidang Keahlian</th>', '<th>Program Keahlian</th>', '<th>Jumlah</th>', 'Aksi</th>'], false)
            ->assertSeeInOrder([$program->bidangKeahlian->nama, $program->nama, '1 konsentrasi keahlian']);
    }

    public function test_daftar_diurutkan_menurut_bidang_lalu_program(): void
    {
        $bidangA = BidangKeahlian::factory()->create(['nama' => 'A Bidang']);
        $bidangB = BidangKeahlian::factory()->create(['nama' => 'B Bidang']);
        ProgramKeahlian::factory()->for($bidangB)->create(['nama' => 'A Program']);
        ProgramKeahlian::factory()->for($bidangA)->create(['nama' => 'Z Program']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.program-keahlian.index'))
            ->assertSeeInOrder(['A Bidang', 'Z Program', 'B Bidang', 'A Program']);
    }

    public function test_form_tambah_menampilkan_pilihan_bidang_keahlian(): void
    {
        $bidang = BidangKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.program-keahlian.create'))
            ->assertOk()
            ->assertSee($bidang->nama);
    }

    public function test_administrator_menambah_program_keahlian(): void
    {
        $bidang = BidangKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.program-keahlian.store'), [
                'id_bidang_keahlian' => $bidang->id_bidang_keahlian,
                'nama' => 'Perhotelan',
            ])
            ->assertRedirect(route('admin.master-data.program-keahlian.index'));

        $this->assertDatabaseHas('tb_program_keahlian', [
            'id_bidang_keahlian' => $bidang->id_bidang_keahlian,
            'nama' => 'Perhotelan',
        ]);
    }

    public function test_bidang_keahlian_yang_sudah_dihapus_tidak_dapat_dipilih(): void
    {
        $bidang = BidangKeahlian::factory()->create();
        $bidang->delete();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.program-keahlian.store'), [
                'id_bidang_keahlian' => $bidang->id_bidang_keahlian,
                'nama' => 'Perhotelan',
            ])
            ->assertSessionHasErrors(['id_bidang_keahlian' => 'Bidang keahlian yang dipilih tidak valid.']);
    }

    public function test_administrator_mengubah_program_keahlian(): void
    {
        $program = ProgramKeahlian::factory()->create();
        $bidangBaru = BidangKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.program-keahlian.update', $program), [
                'id_bidang_keahlian' => $bidangBaru->id_bidang_keahlian,
                'nama' => 'Pemasaran',
            ])
            ->assertRedirect(route('admin.master-data.program-keahlian.index'));

        $program->refresh();
        $this->assertSame('Pemasaran', $program->nama);
        $this->assertTrue($program->bidangKeahlian->is($bidangBaru));
    }

    public function test_program_keahlian_yang_masih_memiliki_konsentrasi_tidak_dapat_dihapus(): void
    {
        $program = ProgramKeahlian::factory()->has(KonsentrasiKeahlian::factory())->create(['nama' => 'Perhotelan']);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.program-keahlian.destroy', $program))
            ->assertSessionHas('galat', 'Program keahlian Perhotelan masih memiliki konsentrasi keahlian.');

        $this->assertNotSoftDeleted($program);
    }

    public function test_program_keahlian_tanpa_konsentrasi_dapat_dihapus(): void
    {
        $program = ProgramKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.program-keahlian.destroy', $program))
            ->assertRedirect(route('admin.master-data.program-keahlian.index'));

        $this->assertSoftDeleted($program);
    }
}
