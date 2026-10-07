<?php

namespace App\Models;

use Database\Factories\KonsentrasiKeahlianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id_program_keahlian', 'nama', 'singkatan'])]
class KonsentrasiKeahlian extends Model
{
    /** @use HasFactory<KonsentrasiKeahlianFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_konsentrasi_keahlian';

    protected $primaryKey = 'id_konsentrasi_keahlian';

    /**
     * @return BelongsTo<ProgramKeahlian, $this>
     */
    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class, 'id_program_keahlian', 'id_program_keahlian');
    }

    /**
     * @return HasMany<Rombel, $this>
     */
    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class, 'id_konsentrasi_keahlian', 'id_konsentrasi_keahlian');
    }

    /**
     * Penugasan Ketua Jurusan.
     *
     * @return HasMany<Penugasan, $this>
     */
    public function penugasan(): HasMany
    {
        return $this->hasMany(Penugasan::class, 'id_konsentrasi_keahlian', 'id_konsentrasi_keahlian');
    }
}
