<?php

namespace App\Models;

use App\Enums\KodePeran;
use Database\Factories\PenugasanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Penugasan peran kontekstual (Kepala/Wakil Kepala Sekolah, Ketua Jurusan,
 * Wali Kelas) kepada tenaga pendidik untuk satu tahun ajaran.
 */
#[Fillable([
    'id_tenaga_pendidik', 'id_tahun_ajaran', 'id_peran',
    'id_rombel', 'id_konsentrasi_keahlian', 'id_satuan_pendidikan', 'bidang',
])]
class Penugasan extends Model
{
    /** @use HasFactory<PenugasanFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_penugasan';

    protected $primaryKey = 'id_penugasan';

    /**
     * @return BelongsTo<TenagaPendidik, $this>
     */
    public function tenagaPendidik(): BelongsTo
    {
        return $this->belongsTo(TenagaPendidik::class, 'id_tenaga_pendidik', 'id_tenaga_pendidik');
    }

    /**
     * @return BelongsTo<TahunAjaran, $this>
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    /**
     * @return BelongsTo<Peran, $this>
     */
    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'id_peran', 'id_peran');
    }

    /**
     * @return BelongsTo<Rombel, $this>
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'id_rombel', 'id_rombel');
    }

    /**
     * @return BelongsTo<KonsentrasiKeahlian, $this>
     */
    public function konsentrasiKeahlian(): BelongsTo
    {
        return $this->belongsTo(KonsentrasiKeahlian::class, 'id_konsentrasi_keahlian', 'id_konsentrasi_keahlian');
    }

    /**
     * @return BelongsTo<SatuanPendidikan, $this>
     */
    public function satuanPendidikan(): BelongsTo
    {
        return $this->belongsTo(SatuanPendidikan::class, 'id_satuan_pendidikan', 'id_satuan_pendidikan');
    }

    /**
     * Keterangan konteks penugasan, mis. "Rombel X PH 1" atau "SMK Puspita Bangsa — Kurikulum".
     * Membutuhkan relasi peran, rombel, konsentrasiKeahlian, dan satuanPendidikan.
     */
    public function konteks(): string
    {
        return match ($this->peran->kode) {
            KodePeran::WaliKelas => 'Rombel '.($this->rombel?->nama ?? '—'),
            KodePeran::KetuaJurusan => $this->konsentrasiKeahlian?->nama ?? '—',
            KodePeran::WakilKepalaSekolah => ($this->satuanPendidikan?->nama ?? '—').' — '.$this->bidang,
            default => $this->satuanPendidikan?->nama ?? '—',
        };
    }
}
