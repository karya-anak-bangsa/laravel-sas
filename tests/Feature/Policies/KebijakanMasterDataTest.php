<?php

namespace Tests\Feature\Policies;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\SatuanPendidikan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KebijakanMasterDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Model master data yang hanya boleh dikelola Administrator.
     *
     * @return array<string, array{class-string<Model>}>
     */
    public static function modelMasterData(): array
    {
        return [
            'satuan pendidikan' => [SatuanPendidikan::class],
        ];
    }

    /**
     * @return array<string, array{KodePeran}>
     */
    public static function peranSelainAdministrator(): array
    {
        return collect(KodePeran::cases())
            ->reject(fn (KodePeran $kode) => $kode === KodePeran::Administrator)
            ->mapWithKeys(fn (KodePeran $kode) => [$kode->value => [$kode]])
            ->all();
    }

    /**
     * @param  class-string<Model>  $kelasModel
     */
    #[DataProvider('modelMasterData')]
    public function test_administrator_boleh_mengelola_master_data(string $kelasModel): void
    {
        $admin = Pengguna::factory()->denganPeran(KodePeran::Administrator)->create();
        $model = $kelasModel::factory()->create();

        $this->assertTrue($admin->can('viewAny', $kelasModel));
        $this->assertTrue($admin->can('create', $kelasModel));
        $this->assertTrue($admin->can('view', $model));
        $this->assertTrue($admin->can('update', $model));
        $this->assertTrue($admin->can('delete', $model));
    }

    #[DataProvider('peranSelainAdministrator')]
    public function test_peran_selain_administrator_tidak_boleh_mengelola_master_data(KodePeran $peran): void
    {
        $pengguna = Pengguna::factory()->denganPeran($peran)->create();

        foreach (self::modelMasterData() as [$kelasModel]) {
            $model = $kelasModel::factory()->create();

            $this->assertFalse($pengguna->can('viewAny', $kelasModel), "viewAny {$kelasModel}");
            $this->assertFalse($pengguna->can('create', $kelasModel), "create {$kelasModel}");
            $this->assertFalse($pengguna->can('view', $model), "view {$kelasModel}");
            $this->assertFalse($pengguna->can('update', $model), "update {$kelasModel}");
            $this->assertFalse($pengguna->can('delete', $model), "delete {$kelasModel}");
        }
    }
}
