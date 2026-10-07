<?php

namespace App\Enums;

/**
 * Kode peran yang disimpan di tb_peran.kode.
 *
 * Wali Kelas, Ketua Jurusan, serta Kepala/Wakil Kepala Sekolah juga dicatat
 * sebagai penugasan per tahun ajaran (rombel, konsentrasi keahlian, satuan
 * pendidikan) begitu master data tersebut tersedia.
 */
enum KodePeran: string
{
    case Administrator = 'administrator';
    case KepalaSekolah = 'kepala_sekolah';
    case WakilKepalaSekolah = 'wakil_kepala_sekolah';
    case KetuaJurusan = 'ketua_jurusan';
    case WaliKelas = 'wali_kelas';
    case TenagaPendidik = 'tenaga_pendidik';
    case TenagaKependidikan = 'tenaga_kependidikan';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::KepalaSekolah => 'Kepala Sekolah',
            self::WakilKepalaSekolah => 'Wakil Kepala Sekolah',
            self::KetuaJurusan => 'Ketua Jurusan',
            self::WaliKelas => 'Wali Kelas',
            self::TenagaPendidik => 'Tenaga Pendidik',
            self::TenagaKependidikan => 'Tenaga Kependidikan',
        };
    }
}
