<?php

namespace App\Enums;

/**
 * Rentang penghasilan bulanan orang tua/wali (referensi Dapodik).
 */
enum PenghasilanOrangTua: string
{
    case KurangDari500Ribu = 'kurang_500rb';
    case Antara500RibuDan1Juta = '500rb_1jt';
    case Antara1JutaDan2Juta = '1jt_2jt';
    case Antara2JutaDan5Juta = '2jt_5jt';
    case Antara5JutaDan20Juta = '5jt_20jt';
    case LebihDari20Juta = 'lebih_20jt';
    case TidakBerpenghasilan = 'tidak_berpenghasilan';

    public function label(): string
    {
        return match ($this) {
            self::KurangDari500Ribu => 'Kurang dari Rp500.000',
            self::Antara500RibuDan1Juta => 'Rp500.000 – Rp999.999',
            self::Antara1JutaDan2Juta => 'Rp1.000.000 – Rp1.999.999',
            self::Antara2JutaDan5Juta => 'Rp2.000.000 – Rp4.999.999',
            self::Antara5JutaDan20Juta => 'Rp5.000.000 – Rp20.000.000',
            self::LebihDari20Juta => 'Lebih dari Rp20.000.000',
            self::TidakBerpenghasilan => 'Tidak berpenghasilan',
        };
    }
}
