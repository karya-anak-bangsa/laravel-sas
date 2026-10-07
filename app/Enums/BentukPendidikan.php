<?php

namespace App\Enums;

/**
 * Bentuk pendidikan satuan pendidikan (istilah Dapodik).
 *
 * Cakupan SAS saat ini SMP dan SMK; MTs disiapkan agar dapat ditambahkan
 * tanpa mengubah struktur data.
 */
enum BentukPendidikan: string
{
    case Smp = 'smp';
    case Smk = 'smk';
    case Mts = 'mts';

    public function label(): string
    {
        return match ($this) {
            self::Smp => 'SMP',
            self::Smk => 'SMK',
            self::Mts => 'MTs',
        };
    }

    /**
     * Tingkat yang tersedia pada bentuk pendidikan ini.
     *
     * @return list<Tingkat>
     */
    public function tingkat(): array
    {
        return match ($this) {
            self::Smp, self::Mts => [Tingkat::VII, Tingkat::VIII, Tingkat::IX],
            self::Smk => [Tingkat::X, Tingkat::XI, Tingkat::XII],
        };
    }

    /**
     * Apakah rombel pada bentuk pendidikan ini memiliki konsentrasi keahlian.
     */
    public function memilikiKonsentrasiKeahlian(): bool
    {
        return $this === self::Smk;
    }
}
