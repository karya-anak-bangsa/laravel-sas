<?php

namespace App\Models;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\ModaTransportasi;
use App\Enums\TempatTinggal;
use Database\Factories\MuridFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Murid. NIK, No. KK, dan data orang tua adalah data pribadi: akses lewat
 * Policy dan jangan pernah ditulis ke log.
 */
#[Fillable([
    'nama_lengkap', 'jenis_kelamin', 'nisn', 'nik', 'no_kk', 'no_seri_ijazah', 'no_seri_skhus',
    'tempat_lahir', 'tanggal_lahir', 'agama',
    'alamat_jalan', 'rt', 'rw', 'dusun', 'kelurahan_desa', 'kecamatan', 'kode_pos',
    'moda_transportasi', 'tempat_tinggal', 'nomor_hp', 'email', 'no_kps_pkh', 'no_kip',
])]
class Murid extends Model
{
    /** @use HasFactory<MuridFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_murid';

    protected $primaryKey = 'id_murid';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis_kelamin' => JenisKelamin::class,
            'tanggal_lahir' => 'date',
            'agama' => Agama::class,
            'moda_transportasi' => ModaTransportasi::class,
            'tempat_tinggal' => TempatTinggal::class,
        ];
    }

    /**
     * @return HasMany<MuridBerkebutuhanKhusus, $this>
     */
    public function berkebutuhanKhusus(): HasMany
    {
        return $this->hasMany(MuridBerkebutuhanKhusus::class, 'id_murid', 'id_murid');
    }

    /**
     * Orang tua/wali beserta hubungannya (pivot `hubungan`).
     *
     * @return BelongsToMany<OrangTuaWali, $this, MuridOrangTua>
     */
    public function orangTuaWali(): BelongsToMany
    {
        return $this->belongsToMany(OrangTuaWali::class, 'tb_murid_orang_tua', 'id_murid', 'id_orang_tua_wali', 'id_murid', 'id_orang_tua_wali')
            ->using(MuridOrangTua::class)
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    /**
     * Daftar kebutuhan khusus dalam teks, atau "Tidak ada".
     */
    public function keteranganBerkebutuhanKhusus(): string
    {
        return $this->berkebutuhanKhusus->map(fn (MuridBerkebutuhanKhusus $baris) => $baris->kode->label())->join(', ')
            ?: 'Tidak ada';
    }
}
