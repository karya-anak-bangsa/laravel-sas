<?php

namespace Tests\Feature\Pengguna;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PenggunaTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_disimpan_di_tb_pengguna_dengan_password_ter_hash(): void
    {
        $pengguna = Pengguna::factory()->create(['password' => 'rahasia123']);

        $this->assertDatabaseHas('tb_pengguna', [
            'id_pengguna' => $pengguna->id_pengguna,
            'email' => $pengguna->email,
            'aktif' => true,
        ]);
        $this->assertTrue(Hash::check('rahasia123', $pengguna->password));
        $this->assertArrayNotHasKey('password', $pengguna->toArray());
    }

    public function test_pengguna_memakai_soft_delete(): void
    {
        $pengguna = Pengguna::factory()->create();

        $pengguna->delete();

        $this->assertSoftDeleted('tb_pengguna', ['id_pengguna' => $pengguna->id_pengguna]);
    }

    public function test_provider_autentikasi_memakai_model_pengguna(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->assertInstanceOf(Pengguna::class, Auth::getProvider()->retrieveById($pengguna->id_pengguna));
    }
}
