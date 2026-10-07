<?php

namespace App\Enums;

/**
 * Kode peran yang disimpan di tb_peran.kode.
 *
 * Peran kontekstual (Kepala/Wakil Kepala Sekolah, Ketua Jurusan, Wali Kelas)
 * tidak diberikan lewat tb_pengguna_peran, tetapi berasal dari tb_penugasan
 * pada tahun ajaran aktif.
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

    /**
     * Apakah peran ini berasal dari penugasan per tahun ajaran.
     */
    public function kontekstual(): bool
    {
        return match ($this) {
            self::KepalaSekolah, self::WakilKepalaSekolah, self::KetuaJurusan, self::WaliKelas => true,
            default => false,
        };
    }

    /**
     * @return list<self>
     */
    public static function daftarKontekstual(): array
    {
        return array_values(array_filter(self::cases(), fn (self $kode) => $kode->kontekstual()));
    }

    /**
     * Peran yang diberikan langsung ke akun (tb_pengguna_peran).
     *
     * @return list<self>
     */
    public static function daftarTetap(): array
    {
        return array_values(array_filter(self::cases(), fn (self $kode) => ! $kode->kontekstual()));
    }
}
