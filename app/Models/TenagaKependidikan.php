<?php

namespace App\Models;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use Database\Factories\TenagaKependidikanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tenaga kependidikan. Kolom sementara mengikuti data KTP (status: TBD).
 */
#[Fillable([
    'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
    'alamat', 'rt', 'rw', 'kelurahan_desa', 'kecamatan', 'agama',
])]
class TenagaKependidikan extends Model
{
    /** @use HasFactory<TenagaKependidikanFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_tenaga_kependidikan';

    protected $primaryKey = 'id_tenaga_kependidikan';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'jenis_kelamin' => JenisKelamin::class,
            'agama' => Agama::class,
        ];
    }

    /**
     * Akun login (opsional).
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
