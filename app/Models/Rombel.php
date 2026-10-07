<?php

namespace App\Models;

use App\Enums\Tingkat;
use Database\Factories\RombelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id_tahun_ajaran', 'id_satuan_pendidikan', 'id_konsentrasi_keahlian', 'tingkat', 'nama'])]
class Rombel extends Model
{
    /** @use HasFactory<RombelFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_rombel';

    protected $primaryKey = 'id_rombel';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tingkat' => Tingkat::class,
        ];
    }

    /**
     * @return BelongsTo<TahunAjaran, $this>
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    /**
     * @return BelongsTo<SatuanPendidikan, $this>
     */
    public function satuanPendidikan(): BelongsTo
    {
        return $this->belongsTo(SatuanPendidikan::class, 'id_satuan_pendidikan', 'id_satuan_pendidikan');
    }

    /**
     * Hanya terisi untuk rombel SMK.
     *
     * @return BelongsTo<KonsentrasiKeahlian, $this>
     */
    public function konsentrasiKeahlian(): BelongsTo
    {
        return $this->belongsTo(KonsentrasiKeahlian::class, 'id_konsentrasi_keahlian', 'id_konsentrasi_keahlian');
    }

    /**
     * Penugasan Wali Kelas.
     *
     * @return HasMany<Penugasan, $this>
     */
    public function penugasan(): HasMany
    {
        return $this->hasMany(Penugasan::class, 'id_rombel', 'id_rombel');
    }
}
