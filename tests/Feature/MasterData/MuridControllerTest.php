<?php

namespace Tests\Feature\MasterData;

use App\Enums\BerkebutuhanKhusus;
use App\Enums\JenisKelamin;
use App\Enums\KodePeran;
use App\Enums\ModaTransportasi;
use App\Enums\TempatTinggal;
use App\Models\Murid;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MuridControllerTest extends TestCase
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
            'nama_lengkap' => 'Rizky Pratama',
            'jenis_kelamin' => JenisKelamin::L->value,
            'nisn' => '0101234567',
            'nik' => '3674011201100001',
            'no_kk' => '3674010101100009',
            'tempat_lahir' => 'Tangerang Selatan',
            'tanggal_lahir' => '2010-01-12',
            'berkebutuhan_khusus' => [],
            'alamat_jalan' => 'Jl. Kenanga No. 10',
            'rt' => '002',
            'rw' => '005',
            'kelurahan_desa' => 'Serua',
            'kecamatan' => 'Ciputat',
            'kode_pos' => '15414',
            'moda_transportasi' => ModaTransportasi::JalanKaki->value,
            'tempat_tinggal' => TempatTinggal::BersamaOrangTua->value,
            'nomor_hp' => '0812-3456-7890',
        ], $timpa);
    }

    public function test_wali_kelas_tidak_boleh_membuka_daftar_murid(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::WaliKelas)->create();

        $this->actingAs($pengguna)->get(route('admin.master-data.murid.index'))->assertForbidden();
    }

    public function test_administrator_melihat_dan_mencari_murid(): void
    {
        Murid::factory()->create(['nama_lengkap' => 'Rizky Pratama', 'nisn' => '0101234567']);
        Murid::factory()->create(['nama_lengkap' => 'Nabila Putri', 'nisn' => '0109876543']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.murid.index', ['cari' => '010987']))
            ->assertOk()
            ->assertSee('Nabila Putri')
            ->assertDontSee('Rizky Pratama');
    }

    public function test_administrator_menambah_murid_dengan_nomor_hp_dinormalkan(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid())
            ->assertSessionHasNoErrors();

        $murid = Murid::query()->where('nisn', '0101234567')->firstOrFail();
        $this->assertSame('081234567890', $murid->nomor_hp);
        $this->assertSame('Ciputat', $murid->kecamatan);
        $this->assertSame('Tidak ada', $murid->keteranganBerkebutuhanKhusus());
    }

    public function test_murid_dapat_memiliki_lebih_dari_satu_kebutuhan_khusus(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid([
                'berkebutuhan_khusus' => [BerkebutuhanKhusus::B->value, BerkebutuhanKhusus::F->value],
            ]))
            ->assertSessionHasNoErrors();

        $murid = Murid::query()->with('berkebutuhanKhusus')->sole();
        $this->assertSame('B - Tuna Rungu, F - Tuna Wicara', $murid->keteranganBerkebutuhanKhusus());
    }

    public function test_mengubah_kebutuhan_khusus_menghapus_yang_tidak_dipilih(): void
    {
        $murid = Murid::factory()->create();
        $murid->berkebutuhanKhusus()->createMany([['kode' => 'A'], ['kode' => 'Q']]);

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.murid.update', $murid), $this->isianValid([
                'nisn' => $murid->nisn,
                'nik' => $murid->nik,
                'berkebutuhan_khusus' => [BerkebutuhanKhusus::Q->value, BerkebutuhanKhusus::K->value],
            ]))
            ->assertRedirect(route('admin.master-data.murid.edit', $murid))
            ->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing(['Q', 'K'], $murid->berkebutuhanKhusus()->pluck('kode')->map->value->all());
    }

    public function test_isian_wajib_minimal(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.master-data.murid.store'), [])
            ->assertSessionHasErrors([
                'nama_lengkap' => 'Nama lengkap wajib diisi.',
                'jenis_kelamin' => 'Jenis kelamin wajib diisi.',
                'tanggal_lahir' => 'Tanggal lahir wajib diisi.',
            ]);

        $this->actingAs($admin)
            ->post(route('admin.master-data.murid.store'), [
                'nama_lengkap' => 'Rizky Pratama',
                'jenis_kelamin' => JenisKelamin::L->value,
                'tanggal_lahir' => '2010-01-12',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_nomor_identitas_divalidasi_digitnya(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid([
                'nisn' => '12345',
                'nik' => '1234',
                'no_kk' => '12345678901234AB',
                'kode_pos' => '1541',
            ]))
            ->assertSessionHasErrors([
                'nisn' => 'NISN harus terdiri dari 10 digit.',
                'nik' => 'NIK harus terdiri dari 16 digit.',
                'no_kk' => 'Nomor KK harus terdiri dari 16 digit.',
                'kode_pos' => 'Kode pos harus terdiri dari 5 digit.',
            ]);
    }

    public function test_nisn_dan_nik_tidak_boleh_sama_dengan_murid_lain(): void
    {
        $lain = Murid::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid(['nisn' => $lain->nisn, 'nik' => $lain->nik]))
            ->assertSessionHasErrors([
                'nisn' => 'NISN sudah digunakan.',
                'nik' => 'NIK sudah digunakan.',
            ]);
    }

    public function test_nomor_kk_boleh_sama_untuk_murid_bersaudara(): void
    {
        $kakak = Murid::factory()->create(['no_kk' => '3674010101100009']);

        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid(['no_kk' => $kakak->no_kk]))
            ->assertSessionHasNoErrors();
    }

    public function test_nomor_hp_harus_nomor_ponsel_indonesia(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid(['nomor_hp' => '021-7654321']))
            ->assertSessionHasErrors(['nomor_hp' => 'Nomor HP harus nomor ponsel Indonesia yang valid, mis. 081234567890.']);
    }

    public function test_kebutuhan_khusus_di_luar_referensi_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.murid.store'), $this->isianValid(['berkebutuhan_khusus' => ['Z']]))
            ->assertSessionHasErrors(['berkebutuhan_khusus.0' => 'Berkebutuhan khusus yang dipilih tidak valid.']);
    }

    public function test_form_ubah_menandai_kebutuhan_khusus_tersimpan(): void
    {
        $murid = Murid::factory()->create();
        $murid->berkebutuhanKhusus()->create(['kode' => 'H']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.murid.edit', $murid))
            ->assertOk()
            ->assertSee('value="H" checked', false);
    }

    public function test_administrator_menghapus_murid(): void
    {
        $murid = Murid::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.murid.destroy', $murid))
            ->assertRedirect(route('admin.master-data.murid.index'));

        $this->assertSoftDeleted($murid);
    }
}
