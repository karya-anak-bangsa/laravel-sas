<?php

namespace Database\Factories;

use App\Enums\KodePeran;
use App\Models\KonsentrasiKeahlian;
use App\Models\Penugasan;
use App\Models\Peran;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penugasan>
 */
class PenugasanFactory extends Factory
{
    /**
     * Kepala Sekolah secara bawaan.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_tenaga_pendidik' => TenagaPendidik::factory(),
            'id_tahun_ajaran' => TahunAjaran::factory(),
            'id_peran' => fn () => $this->idPeran(KodePeran::KepalaSekolah),
            'id_satuan_pendidikan' => SatuanPendidikan::factory(),
        ];
    }

    /**
     * Wali kelas untuk rombel pada tahun ajaran penugasan.
     */
    public function waliKelas(?Rombel $rombel = null): static
    {
        return $this->state(fn (array $attributes) => [
            'id_peran' => $this->idPeran(KodePeran::WaliKelas),
            'id_satuan_pendidikan' => null,
            // Closure dievaluasi setelah id_tahun_ajaran dibuat, agar rombel berada di tahun ajaran yang sama.
            'id_rombel' => $rombel?->id_rombel
                ?? fn (array $atribut) => Rombel::factory()->state(['id_tahun_ajaran' => $atribut['id_tahun_ajaran']]),
        ]);
    }

    public function ketuaJurusan(?KonsentrasiKeahlian $konsentrasi = null): static
    {
        return $this->state(fn (array $attributes) => [
            'id_peran' => $this->idPeran(KodePeran::KetuaJurusan),
            'id_satuan_pendidikan' => null,
            'id_konsentrasi_keahlian' => $konsentrasi?->id_konsentrasi_keahlian ?? KonsentrasiKeahlian::factory(),
        ]);
    }

    public function wakilKepalaSekolah(string $bidang = 'Kurikulum'): static
    {
        return $this->state(fn (array $attributes) => [
            'id_peran' => $this->idPeran(KodePeran::WakilKepalaSekolah),
            'bidang' => $bidang,
        ]);
    }

    private function idPeran(KodePeran $kode): int
    {
        return Peran::query()->where('kode', $kode)->value('id_peran');
    }
}
