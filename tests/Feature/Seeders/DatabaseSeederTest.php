<?php

namespace Tests\Feature\Seeders;

use App\Models\Murid;
use App\Models\Pengguna;
use App\Models\Semester;
use App\Models\TenagaPendidik;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DataContohSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_data_contoh_mengisi_master_data_tanpa_akun_selain_administrator(): void
    {
        // Jumlah murid diperkecil agar test cepat; bawaannya 30–35 per rombel.
        $this->app->instance(DataContohSeeder::class, new DataContohSeeder(muridMinimal: 2, muridMaksimal: 3));

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Pengguna::query()->sole()->adalahAdministrator());
        $this->assertSame(0, TenagaPendidik::query()->whereNotNull('id_pengguna')->count());

        $tahunAjaranAktif = Semester::query()->aktif()->sole()->tahunAjaran;

        $this->assertSame('2026/2027', $tahunAjaranAktif->nama);
        $this->assertDatabaseCount('tb_rombel', 42);
        $this->assertSame(0, Murid::query()->whereDoesntHave('rombelAktif')->count());
        $this->assertSame(21, $tahunAjaranAktif->rombel()->whereHas('penugasan')->count());
        $this->assertSame(0, $tahunAjaranAktif->rombel()
            ->where(fn ($query) => $query->has('murid', '<', 2)->orHas('murid', '>', 3))
            ->count());
    }
}
