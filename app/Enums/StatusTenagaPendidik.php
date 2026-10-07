<?php

namespace App\Enums;

/**
 * Status kepegawaian tenaga pendidik di yayasan. Tidak ada status honorer.
 */
enum StatusTenagaPendidik: string
{
    case Gtt = 'gtt';
    case Gty = 'gty';

    public function label(): string
    {
        return match ($this) {
            self::Gtt => 'GTT',
            self::Gty => 'GTY',
        };
    }

    public function keterangan(): string
    {
        return match ($this) {
            self::Gtt => 'Guru Tidak Tetap',
            self::Gty => 'Guru Tetap Yayasan',
        };
    }
}
