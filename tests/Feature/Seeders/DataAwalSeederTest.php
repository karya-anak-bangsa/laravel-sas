<?php

namespace Tests\Feature\Seeders;

use App\Models\KonsentrasiKeahlian;
use App\Models\SatuanPendidikan;
use Database\Seeders\DataAwalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataAwalSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_mengisi_satuan_pendidikan_dan_spektrum_keahlian_tanpa_duplikasi(): void
    {
        $this->seed(DataAwalSeeder::class);
        $this->seed(DataAwalSeeder::class);

        $this->assertSame(2, SatuanPendidikan::query()->count());
        $this->assertDatabaseCount('tb_bidang_keahlian', 3);
        $this->assertDatabaseCount('tb_program_keahlian', 5);
        $this->assertSame(
            ['AK', 'BR', 'MP', 'PH', 'RPL'],
            KonsentrasiKeahlian::query()->orderBy('singkatan')->pluck('singkatan')->all(),
        );

        $rpl = KonsentrasiKeahlian::query()->where('singkatan', 'RPL')->firstOrFail();
        $this->assertSame('Pengembangan Perangkat Lunak dan Gim', $rpl->programKeahlian->nama);
        $this->assertSame('Teknologi Informasi', $rpl->programKeahlian->bidangKeahlian->nama);
    }
}
