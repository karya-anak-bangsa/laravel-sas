<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisSemester;
use App\Enums\KodePeran;
use App\Models\Murid;
use App\Models\Pengguna;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnggotaRombelControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Pengguna
    {
        return Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
    }

    public function test_wali_kelas_tidak_boleh_mengubah_anggota_rombel(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::WaliKelas)->create();
        $rombel = Rombel::factory()->create();
        $murid = Murid::factory()->create();

        $this->actingAs($pengguna)
            ->post(route('admin.master-data.rombel.anggota.store', $rombel), ['id_murid' => [$murid->id_murid]])
            ->assertForbidden();

        $this->assertDatabaseCount('tb_rombel_murid', 0);
    }

    public function test_halaman_anggota_hanya_menawarkan_murid_tanpa_rombel_di_tahun_ajaran_yang_sama(): void
    {
        $rombel = Rombel::factory()->create();
        $rombelLain = Rombel::factory()->create(['id_tahun_ajaran' => $rombel->id_tahun_ajaran]);
        $sudahBerombel = Murid::factory()->create(['nama_lengkap' => 'Sudah Berombel']);
        $rombelLain->murid()->attach($sudahBerombel);
        Murid::factory()->create(['nama_lengkap' => 'Belum Berombel']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.anggota.index', $rombel))
            ->assertOk()
            ->assertSee('Belum Berombel')
            ->assertDontSee('Sudah Berombel');
    }

    public function test_administrator_menambahkan_beberapa_murid_sekaligus(): void
    {
        $rombel = Rombel::factory()->create(['nama' => 'VII A']);
        $murid = Murid::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.anggota.store', $rombel), ['id_murid' => $murid->modelKeys()])
            ->assertRedirect(route('admin.master-data.rombel.anggota.index', $rombel))
            ->assertSessionHas('status', '3 murid ditambahkan ke rombel VII A.');

        $this->assertEqualsCanonicalizing($murid->modelKeys(), $rombel->murid()->pluck('tb_murid.id_murid')->all());
    }

    public function test_murid_tidak_boleh_berada_di_dua_rombel_pada_tahun_ajaran_yang_sama(): void
    {
        $rombel = Rombel::factory()->create();
        $rombelLain = Rombel::factory()->create(['id_tahun_ajaran' => $rombel->id_tahun_ajaran]);
        $murid = Murid::factory()->create(['nama_lengkap' => 'Rizky Pratama']);
        $rombelLain->murid()->attach($murid);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.anggota.store', $rombel), ['id_murid' => [$murid->id_murid]])
            ->assertSessionHasErrors(['id_murid' => 'Sudah memiliki rombel pada tahun ajaran ini: Rizky Pratama.']);

        $this->assertSame(0, $rombel->murid()->count());
    }

    public function test_murid_boleh_masuk_rombel_tahun_ajaran_berikutnya(): void
    {
        $rombelLama = Rombel::factory()->create();
        $rombelBaru = Rombel::factory()->create();
        $murid = Murid::factory()->create();
        $rombelLama->murid()->attach($murid);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.anggota.store', $rombelBaru), ['id_murid' => [$murid->id_murid]])
            ->assertSessionHasNoErrors();

        $this->assertCount(2, $murid->rombel);
    }

    public function test_pilihan_murid_wajib_diisi(): void
    {
        $rombel = Rombel::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.rombel.anggota.store', $rombel), [])
            ->assertSessionHasErrors(['id_murid' => 'Murid wajib diisi.']);
    }

    public function test_administrator_mengeluarkan_murid_dari_rombel(): void
    {
        $rombel = Rombel::factory()->create();
        $murid = Murid::factory()->create();
        $rombel->murid()->attach($murid);

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.rombel.anggota.destroy', [$rombel, $murid]))
            ->assertRedirect(route('admin.master-data.rombel.anggota.index', $rombel));

        $this->assertSame(0, $rombel->murid()->count());
        $this->assertNotSoftDeleted($murid);
    }

    public function test_rombel_yang_memiliki_anggota_tidak_dapat_dihapus(): void
    {
        $rombel = Rombel::factory()->create(['nama' => 'VII A']);
        $rombel->murid()->attach(Murid::factory()->create());

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.rombel.destroy', $rombel))
            ->assertSessionHas('galat', 'Rombel VII A masih memiliki anggota. Keluarkan muridnya terlebih dahulu.');

        $this->assertNotSoftDeleted($rombel);
    }

    public function test_daftar_murid_menampilkan_rombel_tahun_ajaran_aktif(): void
    {
        $aktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create();
        $rombelAktif = Rombel::factory()->for($aktif)->create(['nama' => 'VIII Aktif']);
        $rombelLama = Rombel::factory()->create(['nama' => 'VII Lama']);
        $murid = Murid::factory()->create();
        $rombelAktif->murid()->attach($murid);
        $rombelLama->murid()->attach($murid);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.murid.index'))
            ->assertOk()
            ->assertSee('VIII Aktif')
            ->assertDontSee('VII Lama');
    }

    public function test_daftar_rombel_menampilkan_jumlah_anggota(): void
    {
        $rombel = Rombel::factory()->create();
        $rombel->murid()->attach(Murid::factory()->count(2)->create());

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.rombel.index', ['tahun_ajaran' => '']))
            ->assertOk()
            ->assertSee('2 murid');
    }
}
