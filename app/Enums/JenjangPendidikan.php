<?php

namespace App\Enums;

/**
 * Jenjang pendidikan terakhir (tenaga pendidik).
 */
enum JenjangPendidikan: string
{
    case Sma = 'sma';
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';
    case D4 = 'd4';
    case S1 = 's1';
    case S2 = 's2';
    case S3 = 's3';

    public function label(): string
    {
        return match ($this) {
            self::Sma => 'SMA/Sederajat',
            default => strtoupper($this->value),
        };
    }
}
