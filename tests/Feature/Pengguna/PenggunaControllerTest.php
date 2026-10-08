<?php

namespace Tests\Feature\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\TenagaPendidik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PenggunaControllerTest extends TestCase
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
            'email' => 'siti@sekolah.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'aktif' => '1',
            'peran' => [KodePeran::TenagaPendidik->value],
        ], $timpa);
    }

    private function tautkan(TenagaPendidik $tenagaPendidik, Pengguna $pengguna): void
    {
        $tenagaPendidik->forceFill(['id_pengguna' => $pengguna->id_pengguna])->save();
    }

    public function test_tenaga_pendidik_tidak_boleh_membuka_daftar_pengguna(): void
    {
        $pengguna = Pengguna::factory()->denganPeran(KodePeran::TenagaPendidik)->create();

        $this->actingAs($pengguna)->get(route('admin.pengguna.index'))->assertForbidden();
    }

    public function test_administrator_melihat_daftar_pengguna(): void
    {
        $pengguna = Pengguna::factory()->nonaktif()->denganPeran(KodePeran::TenagaPendidik)->create();
        $tenagaPendidik = TenagaPendidik::factory()->create(['nama_lengkap' => 'Siti Aminah']);
        $this->tautkan($tenagaPendidik, $pengguna);

        $this->actingAs($this->admin())
            ->get(route('admin.pengguna.index'))
            ->assertOk()
            ->assertSeeInOrder([$pengguna->email, 'Tenaga Pendidik', 'Siti Aminah', 'Nonaktif']);
    }

    public function test_pencarian_email(): void
    {
        Pengguna::factory()->create(['email' => 'budi@sekolah.test']);
        Pengguna::factory()->create(['email' => 'rina@sekolah.test']);

        $this->actingAs($this->admin())
            ->get(route('admin.pengguna.index', ['cari' => 'rina@']))
            ->assertSee('rina@sekolah.test')
            ->assertDontSee('budi@sekolah.test');
    }

    public function test_administrator_membuat_akun_dengan_peran_dan_tautan_tenaga_pendidik(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid([
                'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
            ]))
            ->assertRedirect(route('admin.pengguna.index'))
            ->assertSessionHasNoErrors();

        $pengguna = Pengguna::query()->where('email', 'siti@sekolah.test')->firstOrFail();
        $this->assertTrue(Hash::check('rahasia123', $pengguna->password));
        $this->assertTrue($pengguna->aktif);
        $this->assertSame([KodePeran::TenagaPendidik], $pengguna->kodePeran()->all());
        $this->assertSame($pengguna->id_pengguna, $tenagaPendidik->refresh()->id_pengguna);
    }

    public function test_akun_baru_dapat_langsung_masuk(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid());

        auth()->logout();

        $this->post(route('admin.masuk'), ['email' => 'siti@sekolah.test', 'password' => 'rahasia123'])
            ->assertRedirect(route('admin.dasbor'));
    }

    public function test_peran_kontekstual_tidak_dapat_diberikan_langsung(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid(['peran' => [KodePeran::WaliKelas->value]]))
            ->assertSessionHasErrors(['peran.0' => 'Peran yang dipilih tidak valid.']);
    }

    public function test_kata_sandi_wajib_saat_membuat_akun_dan_harus_dikonfirmasi(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.pengguna.store'), $this->isianValid(['password' => '', 'password_confirmation' => '']))
            ->assertSessionHasErrors(['password' => 'Kata sandi wajib diisi.']);

        $this->actingAs($admin)
            ->post(route('admin.pengguna.store'), $this->isianValid(['password_confirmation' => 'berbeda123']))
            ->assertSessionHasErrors(['password' => 'Konfirmasi kata sandi tidak cocok.']);
    }

    public function test_email_wajib_diisi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid(['email' => '']))
            ->assertSessionHasErrors(['email' => 'Email wajib diisi.']);
    }

    public function test_email_akun_yang_dihapus_tidak_dapat_dipakai_ulang(): void
    {
        Pengguna::factory()->create(['email' => 'siti@sekolah.test'])->delete();

        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid())
            ->assertSessionHasErrors(['email' => 'Email sudah digunakan.']);
    }

    public function test_tenaga_pendidik_yang_sudah_punya_akun_tidak_dapat_ditautkan_lagi(): void
    {
        $tenagaPendidik = TenagaPendidik::factory()->create();
        $this->tautkan($tenagaPendidik, Pengguna::factory()->create());

        $this->actingAs($this->admin())
            ->post(route('admin.pengguna.store'), $this->isianValid(['id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik]))
            ->assertSessionHasErrors(['id_tenaga_pendidik' => 'Data tenaga pendidik tidak ditemukan atau sudah tertaut ke akun lain.']);
    }

    public function test_mengubah_akun_tanpa_kata_sandi_mempertahankan_kata_sandi_lama(): void
    {
        $pengguna = Pengguna::factory()->create(['password' => 'lama12345']);

        $this->actingAs($this->admin())
            ->put(route('admin.pengguna.update', $pengguna), $this->isianValid([
                'email' => $pengguna->email,
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('lama12345', $pengguna->refresh()->password));
    }

    public function test_administrator_mengatur_ulang_kata_sandi_dan_menonaktifkan_akun(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.pengguna.update', $pengguna), $this->isianValid([
                'email' => $pengguna->email,
                'password' => 'baru123456',
                'password_confirmation' => 'baru123456',
                'aktif' => '0',
            ]))
            ->assertSessionHasNoErrors();

        $pengguna->refresh();
        $this->assertTrue(Hash::check('baru123456', $pengguna->password));
        $this->assertFalse($pengguna->aktif);
    }

    public function test_mengganti_tautan_melepas_tenaga_pendidik_lama(): void
    {
        $pengguna = Pengguna::factory()->create();
        $lama = TenagaPendidik::factory()->create();
        $baru = TenagaPendidik::factory()->create();
        $this->tautkan($lama, $pengguna);

        $this->actingAs($this->admin())
            ->put(route('admin.pengguna.update', $pengguna), $this->isianValid([
                'email' => $pengguna->email,
                'password' => '',
                'password_confirmation' => '',
                'id_tenaga_pendidik' => $baru->id_tenaga_pendidik,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertNull($lama->refresh()->id_pengguna);
        $this->assertSame($pengguna->id_pengguna, $baru->refresh()->id_pengguna);
    }

    public function test_administrator_tidak_dapat_menonaktifkan_atau_mencabut_peran_administrator_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.pengguna.update', $admin), $this->isianValid([
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'aktif' => '0',
                'peran' => [KodePeran::TenagaPendidik->value],
            ]))
            ->assertSessionHasErrors([
                'aktif' => 'Anda tidak dapat menonaktifkan akun sendiri.',
                'peran' => 'Anda tidak dapat mencabut peran Administrator dari akun sendiri.',
            ]);

        $this->assertTrue($admin->refresh()->aktif);
        $this->assertTrue($admin->adalahAdministrator());
    }

    public function test_administrator_menghapus_akun_lain_dan_melepas_tautannya(): void
    {
        $pengguna = Pengguna::factory()->create();
        $tenagaPendidik = TenagaPendidik::factory()->create();
        $this->tautkan($tenagaPendidik, $pengguna);

        $this->actingAs($this->admin())
            ->delete(route('admin.pengguna.destroy', $pengguna))
            ->assertRedirect(route('admin.pengguna.index'));

        $this->assertSoftDeleted($pengguna);
        $this->assertNull($tenagaPendidik->refresh()->id_pengguna);
    }

    public function test_administrator_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.pengguna.destroy', $admin))
            ->assertSessionHas('galat', 'Akun yang sedang Anda pakai tidak dapat dihapus.');

        $this->assertNotSoftDeleted($admin);
    }
}
