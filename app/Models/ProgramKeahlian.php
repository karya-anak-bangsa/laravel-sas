<?php

namespace App\Models;

use Database\Factories\ProgramKeahlianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id_bidang_keahlian', 'nama'])]
class ProgramKeahlian extends Model
{
    /** @use HasFactory<ProgramKeahlianFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_program_keahlian';

    protected $primaryKey = 'id_program_keahlian';

    /**
     * @return BelongsTo<BidangKeahlian, $this>
     */
    public function bidangKeahlian(): BelongsTo
    {
        return $this->belongsTo(BidangKeahlian::class, 'id_bidang_keahlian', 'id_bidang_keahlian');
    }

    /**
     * @return HasMany<KonsentrasiKeahlian, $this>
     */
    public function konsentrasiKeahlian(): HasMany
    {
        return $this->hasMany(KonsentrasiKeahlian::class, 'id_program_keahlian', 'id_program_keahlian');
    }
}
