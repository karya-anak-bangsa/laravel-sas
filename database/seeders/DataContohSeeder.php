<?php

namespace Database\Seeders;

use App\Enums\Agama;
use App\Enums\BentukPendidikan;
use App\Enums\BerkebutuhanKhusus;
use App\Enums\HubunganOrangTua;
use App\Enums\JenisKelamin;
use App\Enums\JenisSemester;
use App\Enums\JenjangPendidikan;
use App\Enums\KodePeran;
use App\Enums\ModaTransportasi;
use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use App\Enums\TempatTinggal;
use App\Enums\Tingkat;
use App\Models\KonsentrasiKeahlian;
use App\Models\Murid;
use App\Models\OrangTuaWali;
use App\Models\Penugasan;
use App\Models\Peran;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use App\Models\TenagaKependidikan;
use App\Models\TenagaPendidik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Data contoh master data untuk pengembangan lokal (tidak untuk production).
 *
 * Tidak membuat akun pengguna: tenaga pendidik/kependidikan dibiarkan tanpa akun.
 * Membutuhkan DataAwalSeeder (satuan pendidikan dan spektrum keahlian).
 */
class DataContohSeeder extends Seeder
{
    private const MURID_PER_ROMBEL = 5;

    /**
     * Jumlah rombel per tingkat untuk tiap konsentrasi keahlian SMK.
     *
     * @var array<string, int>
     */
    private const ROMBEL_SMK = ['PH' => 2, 'RPL' => 1, 'MP' => 1, 'BR' => 1, 'AK' => 1];

    private SatuanPendidikan $smp;

    private SatuanPendidikan $smk;

    /** @var Collection<string, KonsentrasiKeahlian> */
    private Collection $konsentrasi;

    public function run(): void
    {
        $this->smp = SatuanPendidikan::query()->where('bentuk_pendidikan', BentukPendidikan::Smp)->firstOrFail();
        $this->smk = SatuanPendidikan::query()->where('bentuk_pendidikan', BentukPendidikan::Smk)->firstOrFail();
        $this->konsentrasi = KonsentrasiKeahlian::query()->get()->keyBy('singkatan');

        $tahunLalu = TahunAjaran::factory()->denganSemester()->create(['nama' => '2025/2026']);
        $tahunAktif = TahunAjaran::factory()->denganSemester(JenisSemester::Ganjil)->create(['nama' => '2026/2027']);

        $rombelLalu = $this->buatRombel($tahunLalu);
        $rombelAktif = $this->buatRombel($tahunAktif);

        $this->isiAnggotaRombel($rombelAktif, $rombelLalu);

        $tenagaPendidikSmp = $this->buatTenagaPendidik($this->smp, 8);
        $tenagaPendidikSmk = $this->buatTenagaPendidik($this->smk, 26);
        $this->buatPenugasan($tahunAktif, $rombelAktif, $tenagaPendidikSmp, $tenagaPendidikSmk);

        TenagaKependidikan::factory()
            ->count(6)
            ->sequence(fn ($urutan) => $this->identitas($urutan->index % 2 === 0 ? JenisKelamin::L : JenisKelamin::P))
            ->create();
    }

    /**
     * Rombel SMP (1 per tingkat) dan SMK (6 per tingkat) untuk satu tahun ajaran.
     *
     * @return Collection<string, Rombel> Dikunci dengan nama rombel, mis. "X PH 1".
     */
    private function buatRombel(TahunAjaran $tahunAjaran): Collection
    {
        $daftar = collect();

        foreach (BentukPendidikan::Smp->tingkat() as $tingkat) {
            $daftar->push(Rombel::factory()->create([
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'id_satuan_pendidikan' => $this->smp->id_satuan_pendidikan,
                'tingkat' => $tingkat,
                'nama' => $tingkat->name,
            ]));
        }

        foreach (BentukPendidikan::Smk->tingkat() as $tingkat) {
            foreach (self::ROMBEL_SMK as $singkatan => $jumlah) {
                for ($nomor = 1; $nomor <= $jumlah; $nomor++) {
                    $daftar->push(Rombel::factory()->create([
                        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                        'id_satuan_pendidikan' => $this->smk->id_satuan_pendidikan,
                        'id_konsentrasi_keahlian' => $this->konsentrasi[$singkatan]->id_konsentrasi_keahlian,
                        'tingkat' => $tingkat,
                        'nama' => "{$tingkat->name} {$singkatan} {$nomor}",
                    ]));
                }
            }
        }

        return $daftar->keyBy('nama');
    }

    /**
     * Isi setiap rombel aktif dengan murid baru. Murid di tingkat kedua dan ketiga
     * juga dicatat sebagai anggota rombel setingkat di bawahnya pada tahun lalu.
     *
     * @param  Collection<string, Rombel>  $rombelAktif
     * @param  Collection<string, Rombel>  $rombelLalu
     */
    private function isiAnggotaRombel(Collection $rombelAktif, Collection $rombelLalu): void
    {
        $urutan = 0;
        $muridSebelumnya = null;

        foreach ($rombelAktif as $rombel) {
            $namaRombelLalu = $this->namaRombelTingkatSebelumnya($rombel);

            for ($i = 0; $i < self::MURID_PER_ROMBEL; $i++, $urutan++) {
                // Setiap murid ke-12 adalah saudara kandung murid sebelumnya: No. KK dan orang tua sama.
                $kakak = $urutan % 12 === 11 ? $muridSebelumnya : null;
                $murid = $this->buatMurid($rombel->tingkat, $urutan, $kakak);

                $murid->rombel()->attach($rombel->id_rombel);

                if ($namaRombelLalu && $rombelLalu->has($namaRombelLalu)) {
                    $murid->rombel()->attach($rombelLalu[$namaRombelLalu]->id_rombel);
                }

                $muridSebelumnya = $murid;
            }
        }
    }

    private function namaRombelTingkatSebelumnya(Rombel $rombel): ?string
    {
        $tingkatLalu = Tingkat::tryFrom($rombel->tingkat->value - 1);
        $bentukPendidikan = $rombel->id_satuan_pendidikan === $this->smp->id_satuan_pendidikan
            ? BentukPendidikan::Smp
            : BentukPendidikan::Smk;

        if (! $tingkatLalu || ! in_array($tingkatLalu, $bentukPendidikan->tingkat(), true)) {
            return null;
        }

        return preg_replace('/^'.$rombel->tingkat->name.'\b/', $tingkatLalu->name, $rombel->nama);
    }

    private function buatMurid(Tingkat $tingkat, int $urutan, ?Murid $kakak): Murid
    {
        $jenisKelamin = $urutan % 2 === 0 ? JenisKelamin::L : JenisKelamin::P;
        // Umur murid kira-kira tingkat + 5 tahun (kelas VII ± 12 tahun).
        $umur = $tingkat->value + 5;

        $murid = Murid::factory()->create([
            ...$this->identitas($jenisKelamin),
            'tanggal_lahir' => fake()->dateTimeBetween("-{$umur} years -11 months", "-{$umur} years")->format('Y-m-d'),
            'no_kk' => $kakak->no_kk ?? fake()->numerify('3674############'),
            'no_seri_ijazah' => $urutan % 3 === 0 ? null : fake()->bothify('DN-01/D-SD/##/#######'),
            'moda_transportasi' => fake()->randomElement(ModaTransportasi::cases()),
            'tempat_tinggal' => $urutan % 20 === 7 ? TempatTinggal::BersamaWali : TempatTinggal::BersamaOrangTua,
            'email' => $urutan % 4 === 0 ? fake()->unique()->safeEmail() : null,
            'no_kip' => $urutan % 6 === 0 ? fake()->numerify('#######') : null,
        ]);

        // Sebagian kecil murid berkebutuhan khusus, ada yang lebih dari satu.
        $kebutuhanKhusus = match ($urutan % 25) {
            5 => [BerkebutuhanKhusus::K],
            17 => [BerkebutuhanKhusus::H, BerkebutuhanKhusus::K],
            default => [],
        };

        foreach ($kebutuhanKhusus as $kode) {
            $murid->berkebutuhanKhusus()->create(['kode' => $kode]);
        }

        if ($kakak) {
            $murid->orangTuaWali()->attach(
                $kakak->orangTuaWali->mapWithKeys(fn (OrangTuaWali $orangTua) => [
                    $orangTua->id_orang_tua_wali => ['hubungan' => $orangTua->pivot->hubungan],
                ])
            );

            return $murid;
        }

        $murid->orangTuaWali()->attach($this->buatOrangTua(JenisKelamin::L), ['hubungan' => HubunganOrangTua::AyahKandung]);
        $murid->orangTuaWali()->attach($this->buatOrangTua(JenisKelamin::P), ['hubungan' => HubunganOrangTua::IbuKandung]);

        if ($murid->tempat_tinggal === TempatTinggal::BersamaWali) {
            $murid->orangTuaWali()->attach($this->buatOrangTua(JenisKelamin::P), ['hubungan' => HubunganOrangTua::Wali]);
        }

        return $murid;
    }

    private function buatOrangTua(JenisKelamin $jenisKelamin): int
    {
        return OrangTuaWali::factory()->create([
            'nama' => $this->namaOrang($jenisKelamin),
            'pendidikan' => fake()->randomElement([PendidikanOrangTua::Smp, PendidikanOrangTua::Sma, PendidikanOrangTua::D3, PendidikanOrangTua::D4S1]),
            'pekerjaan' => $jenisKelamin === JenisKelamin::L
                ? fake()->randomElement([PekerjaanOrangTua::KaryawanSwasta, PekerjaanOrangTua::Wiraswasta, PekerjaanOrangTua::PnsTniPolri, PekerjaanOrangTua::Buruh])
                : fake()->randomElement([PekerjaanOrangTua::TidakBekerja, PekerjaanOrangTua::KaryawanSwasta, PekerjaanOrangTua::PedagangKecil]),
            'penghasilan' => fake()->randomElement([PenghasilanOrangTua::Antara1JutaDan2Juta, PenghasilanOrangTua::Antara2JutaDan5Juta, PenghasilanOrangTua::Antara5JutaDan20Juta]),
        ])->id_orang_tua_wali;
    }

    /**
     * @return Collection<int, TenagaPendidik>
     */
    private function buatTenagaPendidik(SatuanPendidikan $satuanPendidikan, int $jumlah): Collection
    {
        return collect(range(1, $jumlah))->map(function (int $nomor) use ($satuanPendidikan) {
            $jenisKelamin = $nomor % 2 === 0 ? JenisKelamin::P : JenisKelamin::L;
            $factory = TenagaPendidik::factory();

            if ($nomor % 3 !== 0) {
                $factory = $factory->gty();
            }

            return $factory->create([
                'id_satuan_pendidikan' => $satuanPendidikan->id_satuan_pendidikan,
                'nama_lengkap' => $this->namaOrang($jenisKelamin),
                // Sebagian belum memiliki NUPTK.
                'nuptk' => $nomor % 4 === 0 ? null : fake()->unique()->numerify('################'),
                'pendidikan_terakhir' => match ($nomor % 7) {
                    0 => JenjangPendidikan::S2,
                    5 => JenjangPendidikan::D3,
                    default => JenjangPendidikan::S1,
                },
            ]);
        });
    }

    /**
     * Penugasan tahun ajaran aktif: kepala sekolah, wakil kepala sekolah,
     * ketua jurusan per konsentrasi keahlian, dan wali kelas setiap rombel.
     *
     * @param  Collection<string, Rombel>  $rombel
     * @param  Collection<int, TenagaPendidik>  $tenagaPendidikSmp
     * @param  Collection<int, TenagaPendidik>  $tenagaPendidikSmk
     */
    private function buatPenugasan(TahunAjaran $tahunAjaran, Collection $rombel, Collection $tenagaPendidikSmp, Collection $tenagaPendidikSmk): void
    {
        $peran = Peran::query()->pluck('id_peran', 'kode');
        $tugaskan = fn (TenagaPendidik $tenagaPendidik, KodePeran $kode, array $konteks) => Penugasan::query()->create([
            'id_tenaga_pendidik' => $tenagaPendidik->id_tenaga_pendidik,
            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
            'id_peran' => $peran[$kode->value],
            ...$konteks,
        ]);

        $tugaskan($tenagaPendidikSmp[0], KodePeran::KepalaSekolah, ['id_satuan_pendidikan' => $this->smp->id_satuan_pendidikan]);
        $tugaskan($tenagaPendidikSmp[1], KodePeran::WakilKepalaSekolah, ['id_satuan_pendidikan' => $this->smp->id_satuan_pendidikan, 'bidang' => 'Kurikulum']);

        $tugaskan($tenagaPendidikSmk[0], KodePeran::KepalaSekolah, ['id_satuan_pendidikan' => $this->smk->id_satuan_pendidikan]);
        $tugaskan($tenagaPendidikSmk[1], KodePeran::WakilKepalaSekolah, ['id_satuan_pendidikan' => $this->smk->id_satuan_pendidikan, 'bidang' => 'Kurikulum']);
        $tugaskan($tenagaPendidikSmk[2], KodePeran::WakilKepalaSekolah, ['id_satuan_pendidikan' => $this->smk->id_satuan_pendidikan, 'bidang' => 'Kesiswaan']);

        foreach ($this->konsentrasi->values() as $indeks => $konsentrasi) {
            $tugaskan($tenagaPendidikSmk[3 + $indeks], KodePeran::KetuaJurusan, ['id_konsentrasi_keahlian' => $konsentrasi->id_konsentrasi_keahlian]);
        }

        // Wali kelas diambil dari tenaga pendidik tanpa tugas tambahan lain.
        $calonWali = [
            $this->smp->id_satuan_pendidikan => $tenagaPendidikSmp->slice(2)->values(),
            $this->smk->id_satuan_pendidikan => $tenagaPendidikSmk->slice(3 + $this->konsentrasi->count())->values(),
        ];
        $urutanWali = [$this->smp->id_satuan_pendidikan => 0, $this->smk->id_satuan_pendidikan => 0];

        foreach ($rombel as $satuRombel) {
            $idSatuan = $satuRombel->id_satuan_pendidikan;
            $wali = $calonWali[$idSatuan][$urutanWali[$idSatuan]++ % $calonWali[$idSatuan]->count()];

            $tugaskan($wali, KodePeran::WaliKelas, ['id_rombel' => $satuRombel->id_rombel]);
        }
    }

    /**
     * Nama dan jenis kelamin yang serasi, plus agama yang bervariasi.
     *
     * @return array{nama_lengkap: string, jenis_kelamin: JenisKelamin, tempat_lahir: string, agama: Agama}
     */
    private function identitas(JenisKelamin $jenisKelamin): array
    {
        return [
            'nama_lengkap' => $this->namaOrang($jenisKelamin),
            'jenis_kelamin' => $jenisKelamin,
            'tempat_lahir' => fake()->randomElement(['Tangerang Selatan', 'Tangerang', 'Jakarta', 'Bogor', 'Depok', 'Bekasi']),
            'agama' => fake()->randomElement([Agama::Islam, Agama::Islam, Agama::Islam, Agama::Islam, Agama::Kristen, Agama::Katolik, Agama::Hindu, Agama::Buddha]),
        ];
    }

    /**
     * Nama depan + nama belakang tanpa gelar (Faker id_ID `name()` kadang menambah gelar).
     */
    private function namaOrang(JenisKelamin $jenisKelamin): string
    {
        return $jenisKelamin === JenisKelamin::L
            ? fake()->firstNameMale().' '.fake()->lastNameMale()
            : fake()->firstNameFemale().' '.fake()->lastNameFemale();
    }
}
