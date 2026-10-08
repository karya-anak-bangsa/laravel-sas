<?php

namespace Tests\Feature\MasterData;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\TenagaKependidikan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenagaKependidikanControllerTest extends TestCase
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
            'nik' => '3674011705900001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-05-17',
            'jenis_kelamin' => JenisKelamin::L->value,
            'alamat' => 'Jl. Merpati No. 5',
            'rt' => '003',
            'rw' => '007',
            'kelurahan_desa' => 'Cipayung',
            'kecamatan' => 'Ciputat',
            'agama' => Agama::Islam->value,
        ], $timpa);
    }

    public function test_tenaga_kependidikan_tidak_boleh_mengelola_data_tenaga_kependidikan(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaKependidikan)->create();

        $this->actingAs($pengguna)
            ->get(route('admin.master-data.tenaga-kependidikan.index'))
            ->assertForbidden();
    }

    public function test_administrator_melihat_dan_mencari_daftar(): void
    {
        TenagaKependidikan::factory()->create(['nama_lengkap' => 'Ahmad Fauzi', 'nik' => '3674011705900001']);
        TenagaKependidikan::factory()->create(['nama_lengkap' => 'Dewi Lestari', 'nik' => '3674015506920002']);

        $this->actingAs($this->admin())
            ->get(route('admin.master-data.tenaga-kependidikan.index', ['cari' => '36740155']))
            ->assertOk()
            ->assertSee('Dewi Lestari')
            ->assertDontSee('Ahmad Fauzi');
    }

    public function test_administrator_menambah_tenaga_kependidikan(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-kependidikan.store'), $this->isianValid())
            ->assertRedirect(route('admin.master-data.tenaga-kependidikan.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_tenaga_kependidikan', [
            'nik' => '3674011705900001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
            'rt' => '003',
            'kecamatan' => 'Ciputat',
            'agama' => 'islam',
            'id_pengguna' => null,
        ]);
    }

    public function test_hanya_nama_dan_jenis_kelamin_yang_wajib(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.master-data.tenaga-kependidikan.store'), [])
            ->assertSessionHasErrors([
                'nama_lengkap' => 'Nama lengkap wajib diisi.',
                'jenis_kelamin' => 'Jenis kelamin wajib diisi.',
            ]);

        $this->actingAs($admin)
            ->post(route('admin.master-data.tenaga-kependidikan.store'), [
                'nama_lengkap' => 'Ahmad Fauzi',
                'jenis_kelamin' => JenisKelamin::L->value,
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_nik_harus_16_digit_dan_unik(): void
    {
        $lain = TenagaKependidikan::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.master-data.tenaga-kependidikan.store'), $this->isianValid(['nik' => '12345']))
            ->assertSessionHasErrors(['nik' => 'NIK harus terdiri dari 16 digit.']);

        $this->actingAs($admin)
            ->post(route('admin.master-data.tenaga-kependidikan.store'), $this->isianValid(['nik' => $lain->nik]))
            ->assertSessionHasErrors(['nik' => 'NIK sudah digunakan.']);
    }

    public function test_agama_di_luar_pilihan_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.master-data.tenaga-kependidikan.store'), $this->isianValid(['agama' => 'lainnya']))
            ->assertSessionHasErrors(['agama' => 'Agama yang dipilih tidak valid.']);
    }

    public function test_administrator_mengubah_tenaga_kependidikan(): void
    {
        $tenagaKependidikan = TenagaKependidikan::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.master-data.tenaga-kependidikan.update', $tenagaKependidikan),
                $this->isianValid(['nik' => $tenagaKependidikan->nik, 'kecamatan' => 'Pamulang']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Pamulang', $tenagaKependidikan->refresh()->kecamatan);
    }

    public function test_administrator_menghapus_tenaga_kependidikan(): void
    {
        $tenagaKependidikan = TenagaKependidikan::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.master-data.tenaga-kependidikan.destroy', $tenagaKependidikan))
            ->assertRedirect(route('admin.master-data.tenaga-kependidikan.index'));

        $this->assertSoftDeleted($tenagaKependidikan);
    }

    public function test_akun_dapat_ditautkan_ke_tenaga_kependidikan_dari_menu_pengguna(): void
    {
        $tenagaKependidikan = TenagaKependidikan::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), [
                'email' => 'ahmad.fauzi@sekolah.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'aktif' => '1',
                'peran' => [KodePeran::TenagaKependidikan->value],
                'id_tenaga_kependidikan' => $tenagaKependidikan->id_tenaga_kependidikan,
            ])
            ->assertSessionHasNoErrors();

        $pengguna = Pengguna::query()->where('email', 'ahmad.fauzi@sekolah.test')->firstOrFail();
        $this->assertSame($pengguna->id_pengguna, $tenagaKependidikan->refresh()->id_pengguna);
    }
}
