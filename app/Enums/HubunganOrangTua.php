<?php

namespace App\Enums;

enum HubunganOrangTua: string
{
    case AyahKandung = 'ayah_kandung';
    case IbuKandung = 'ibu_kandung';
    case Wali = 'wali';

    public function label(): string
    {
        return match ($this) {
            self::AyahKandung => 'Ayah kandung',
            self::IbuKandung => 'Ibu kandung',
            self::Wali => 'Wali',
        };
    }
}
