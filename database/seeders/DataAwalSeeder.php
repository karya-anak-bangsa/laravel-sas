<?php

namespace Database\Seeders;

use App\Enums\BentukPendidikan;
use App\Models\BidangKeahlian;
use App\Models\SatuanPendidikan;
use Illuminate\Database\Seeder;

/**
 * Data awal yayasan yang dibutuhkan di semua lingkungan, termasuk production.
 *
 * Aman dijalankan berulang: `php artisan db:seed --class=DataAwalSeeder`.
 */
class DataAwalSeeder extends Seeder
{
    /**
     * Spektrum keahlian SMK Puspita Bangsa (Kepmendikbudristek No. 244/M/2024).
     * Bidang => [Program => [[Konsentrasi, Singkatan], ...]].
     *
     * @var array<string, array<string, list<array{string, string}>>>
     */
    private const SPEKTRUM_KEAHLIAN = [
        'Pariwisata' => [
            'Perhotelan' => [['Perhotelan', 'PH']],
        ],
        'Teknologi Informasi' => [
            'Pengembangan Perangkat Lunak dan Gim' => [['Rekayasa Perangkat Lunak', 'RPL']],
        ],
        'Bisnis dan Manajemen' => [
            'Manajemen Perkantoran dan Layanan Bisnis' => [['Manajemen Perkantoran', 'MP']],
            'Pemasaran' => [['Bisnis Retail', 'BR']],
            'Akuntansi dan Keuangan Lembaga' => [['Akuntansi', 'AK']],
        ],
    ];

    public function run(): void
    {
        $this->satuanPendidikan();
        $this->spektrumKeahlian();
    }

    private function satuanPendidikan(): void
    {
        SatuanPendidikan::query()->firstOrCreate(
            ['bentuk_pendidikan' => BentukPendidikan::Smp, 'nama' => 'SMP Puspita Bangsa'],
        );

        SatuanPendidikan::query()->firstOrCreate(
            ['bentuk_pendidikan' => BentukPendidikan::Smk, 'nama' => 'SMK Puspita Bangsa'],
        );
    }

    private function spektrumKeahlian(): void
    {
        foreach (self::SPEKTRUM_KEAHLIAN as $namaBidang => $daftarProgram) {
            $bidang = BidangKeahlian::query()->firstOrCreate(['nama' => $namaBidang]);

            foreach ($daftarProgram as $namaProgram => $daftarKonsentrasi) {
                $program = $bidang->programKeahlian()->firstOrCreate(['nama' => $namaProgram]);

                foreach ($daftarKonsentrasi as [$namaKonsentrasi, $singkatan]) {
                    $program->konsentrasiKeahlian()->firstOrCreate(
                        ['singkatan' => $singkatan],
                        ['nama' => $namaKonsentrasi],
                    );
                }
            }
        }
    }
}
