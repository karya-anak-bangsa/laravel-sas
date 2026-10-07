<?php

namespace App\Enums;

/**
 * Tingkat kelas, disimpan sebagai angka (7–12) dan ditampilkan dalam angka Romawi.
 */
enum Tingkat: int
{
    case VII = 7;
    case VIII = 8;
    case IX = 9;
    case X = 10;
    case XI = 11;
    case XII = 12;

    public function label(): string
    {
        return $this->name;
    }
}
