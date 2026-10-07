<?php

namespace Tests\Feature\MasterData;

use App\Enums\KodePeran;
use App\Models\KonsentrasiKeahlian;
use App\Models\Pengguna;
use App\Models\ProgramKeahlian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonsentrasiKeahlianControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_wali_kelas_tidak_boleh_membuka_daftar(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::WaliKelas)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.master-data.konsentrasi-keahlian.index'))
            ->assertForbidden();
    }

    public function test_administrator_melihat_daftar_dengan_program_dan_bidang(): void
    {
        $konsentrasi = KonsentrasiKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.konsentrasi-keahlian.index'))
            ->assertOk()
            ->assertSeeInOrder([
                $konsentrasi->nama,
                $konsentrasi->singkatan,
                $konsentrasi->programKeahlian->nama,
                $konsentrasi->programKeahlian->bidangKeahlian->nama,
            ]);
    }

    public function test_administrator_menambah_konsentrasi_dengan_singkatan_huruf_besar(): void
    {
        $program = ProgramKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.konsentrasi-keahlian.store'), [
                'id_program_keahlian' => $program->id_program_keahlian,
                'nama' => 'Rekayasa Perangkat Lunak',
                'singkatan' => ' rpl ',
            ])
            ->assertRedirect(route('admin.master-data.konsentrasi-keahlian.index'));

        $this->assertDatabaseHas('tb_konsentrasi_keahlian', [
            'id_program_keahlian' => $program->id_program_keahlian,
            'nama' => 'Rekayasa Perangkat Lunak',
            'singkatan' => 'RPL',
        ]);
    }

    public function test_isian_wajib_divalidasi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.konsentrasi-keahlian.store'), [])
            ->assertSessionHasErrors([
                'id_program_keahlian' => 'Program keahlian wajib diisi.',
                'nama' => 'Nama konsentrasi keahlian wajib diisi.',
                'singkatan' => 'Singkatan wajib diisi.',
            ]);
    }

    public function test_singkatan_tidak_boleh_ganda(): void
    {
        KonsentrasiKeahlian::factory()->create(['singkatan' => 'RPL']);
        $program = ProgramKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.konsentrasi-keahlian.store'), [
                'id_program_keahlian' => $program->id_program_keahlian,
                'nama' => 'Rekayasa Perangkat Lunak',
                'singkatan' => 'rpl',
            ])
            ->assertSessionHasErrors(['singkatan' => 'Singkatan sudah digunakan.']);
    }

    public function test_singkatan_hanya_huruf_dan_angka(): void
    {
        $program = ProgramKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.konsentrasi-keahlian.store'), [
                'id_program_keahlian' => $program->id_program_keahlian,
                'nama' => 'Rekayasa Perangkat Lunak',
                'singkatan' => 'R P-L',
            ])
            ->assertSessionHasErrors(['singkatan' => 'Singkatan hanya boleh berisi huruf dan angka.']);
    }

    public function test_administrator_mengubah_konsentrasi_tanpa_mengganti_singkatan(): void
    {
        $konsentrasi = KonsentrasiKeahlian::factory()->create(['singkatan' => 'PH']);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.konsentrasi-keahlian.update', $konsentrasi), [
                'id_program_keahlian' => $konsentrasi->id_program_keahlian,
                'nama' => 'Perhotelan',
                'singkatan' => 'PH',
            ])
            ->assertRedirect(route('admin.master-data.konsentrasi-keahlian.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Perhotelan', $konsentrasi->refresh()->nama);
    }

    public function test_administrator_menghapus_konsentrasi_keahlian(): void
    {
        $konsentrasi = KonsentrasiKeahlian::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.konsentrasi-keahlian.destroy', $konsentrasi))
            ->assertRedirect(route('admin.master-data.konsentrasi-keahlian.index'));

        $this->assertSoftDeleted($konsentrasi);
    }
}
