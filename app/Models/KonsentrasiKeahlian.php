<?php

namespace App\Models;

use Database\Factories\KonsentrasiKeahlianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
