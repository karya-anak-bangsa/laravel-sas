<?php

namespace App\Enums;

enum TempatTinggal: string
{
    case BersamaOrangTua = 'bersama_orang_tua';
    case BersamaWali = 'bersama_wali';
    case Kos = 'kos';
    case Asrama = 'asrama';
    case PantiAsuhan = 'panti_asuhan';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::BersamaOrangTua => 'Bersama orang tua',
            self::BersamaWali => 'Bersama wali',
            self::Kos => 'Kos',
            self::Asrama => 'Asrama',
            self::PantiAsuhan => 'Panti asuhan',
            self::Lainnya => 'Lainnya',
        };
    }
}
