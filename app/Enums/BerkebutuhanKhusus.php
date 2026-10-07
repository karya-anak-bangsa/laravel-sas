<?php

namespace App\Enums;

/**
 * Jenis berkebutuhan khusus (referensi Kemendikdasmen/Dapodik).
 * "Tidak Ada" tidak disimpan: murid tanpa kebutuhan khusus tidak memiliki baris.
 */
enum BerkebutuhanKhusus: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case C1 = 'C1';
    case D = 'D';
    case D1 = 'D1';
    case E = 'E';
    case F = 'F';
    case H = 'H';
    case I = 'I';
    case J = 'J';
    case K = 'K';
    case N = 'N';
    case O = 'O';
    case P = 'P';
    case Q = 'Q';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::A => 'A - Tuna Netra',
            self::B => 'B - Tuna Rungu',
            self::C => 'C - Tuna Grahita Ringan',
            self::C1 => 'C1 - Tuna Grahita Sedang',
            self::D => 'D - Tuna Daksa Ringan',
            self::D1 => 'D1 - Tuna Daksa Sedang',
            self::E => 'E - Tuna Laras',
            self::F => 'F - Tuna Wicara',
            self::H => 'H - Hiperaktif',
            self::I => 'I - Cerdas Istimewa',
            self::J => 'J - Bakat Istimewa',
            self::K => 'K - Kesulitan Belajar',
            self::N => 'N - Narkoba',
            self::O => 'O - Indigo',
            self::P => 'P - Down Syndrome',
            self::Q => 'Q - Autis',
            self::Lainnya => 'Lainnya',
        };
    }
}
