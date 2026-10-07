<?php

namespace App\Models;

use App\Enums\JenjangPendidikan;
use App\Enums\StatusTenagaPendidik;
use Database\Factories\TenagaPendidikFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'id_satuan_pendidikan', 'nama_lengkap', 'nuptk', 'tempat_lahir', 'tanggal_lahir',
    'pendidikan_terakhir', 'status', 'tmt_gtt', 'tmt_gty', 'masa_kerja_tahun', 'masa_kerja_bulan',
])]
class TenagaPendidik extends Model
{
    /** @use HasFactory<TenagaPendidikFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_tenaga_pendidik';

    protected $primaryKey = 'id_tenaga_pendidik';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'pendidikan_terakhir' => JenjangPendidikan::class,
            'status' => StatusTenagaPendidik::class,
            'tmt_gtt' => 'date',
            'tmt_gty' => 'date',
            'masa_kerja_tahun' => 'integer',
            'masa_kerja_bulan' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<SatuanPendidikan, $this>
     */
    public function satuanPendidikan(): BelongsTo
    {
        return $this->belongsTo(SatuanPendidikan::class, 'id_satuan_pendidikan', 'id_satuan_pendidikan');
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

    /**
     * Masa kerja dalam teks, mis. "5 tahun 3 bulan".
     */
    public function masaKerja(): string
    {
        return "{$this->masa_kerja_tahun} tahun {$this->masa_kerja_bulan} bulan";
    }
}
