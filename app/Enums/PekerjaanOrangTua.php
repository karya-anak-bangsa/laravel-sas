<?php

namespace App\Enums;

/**
 * Pekerjaan orang tua/wali (referensi Dapodik).
 */
enum PekerjaanOrangTua: string
{
    case TidakBekerja = 'tidak_bekerja';
    case Nelayan = 'nelayan';
    case Petani = 'petani';
    case Peternak = 'peternak';
    case PnsTniPolri = 'pns_tni_polri';
    case KaryawanSwasta = 'karyawan_swasta';
    case PedagangKecil = 'pedagang_kecil';
    case PedagangBesar = 'pedagang_besar';
    case Wiraswasta = 'wiraswasta';
    case Wirausaha = 'wirausaha';
    case Buruh = 'buruh';
    case Pensiunan = 'pensiunan';
    case TenagaKerjaIndonesia = 'tki';
    case SudahMeninggal = 'sudah_meninggal';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::TidakBekerja => 'Tidak bekerja',
            self::Nelayan => 'Nelayan',
            self::Petani => 'Petani',
            self::Peternak => 'Peternak',
            self::PnsTniPolri => 'PNS/TNI/Polri',
            self::KaryawanSwasta => 'Karyawan swasta',
            self::PedagangKecil => 'Pedagang kecil',
            self::PedagangBesar => 'Pedagang besar',
            self::Wiraswasta => 'Wiraswasta',
            self::Wirausaha => 'Wirausaha',
            self::Buruh => 'Buruh',
            self::Pensiunan => 'Pensiunan',
            self::TenagaKerjaIndonesia => 'Tenaga Kerja Indonesia',
            self::SudahMeninggal => 'Sudah meninggal',
            self::Lainnya => 'Lainnya',
        };
    }
}
