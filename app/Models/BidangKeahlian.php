<?php

namespace App\Models;

use Database\Factories\BidangKeahlianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama'])]
class BidangKeahlian extends Model
{
    /** @use HasFactory<BidangKeahlianFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_bidang_keahlian';

    protected $primaryKey = 'id_bidang_keahlian';

    /**
     * @return HasMany<ProgramKeahlian, $this>
     */
    public function programKeahlian(): HasMany
    {
        return $this->hasMany(ProgramKeahlian::class, 'id_bidang_keahlian', 'id_bidang_keahlian');
    }
}
