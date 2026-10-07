<?php

namespace App\Enums;

/**
 * Pendidikan orang tua/wali (referensi Dapodik).
 */
enum PendidikanOrangTua: string
{
    case TidakSekolah = 'tidak_sekolah';
    case PutusSd = 'putus_sd';
    case Sd = 'sd';
    case Smp = 'smp';
    case Sma = 'sma';
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';
    case D4S1 = 'd4_s1';
    case S2 = 's2';
    case S3 = 's3';

    public function label(): string
    {
        return match ($this) {
            self::TidakSekolah => 'Tidak sekolah',
            self::PutusSd => 'Putus SD',
            self::Sd => 'SD/Sederajat',
            self::Smp => 'SMP/Sederajat',
            self::Sma => 'SMA/Sederajat',
            self::D1 => 'D1',
            self::D2 => 'D2',
            self::D3 => 'D3',
            self::D4S1 => 'D4/S1',
            self::S2 => 'S2',
            self::S3 => 'S3',
        };
    }
}
