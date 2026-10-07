<?php

namespace App\Models;

use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use Database\Factories\OrangTuaWaliFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Orang tua/wali murid. Data pribadi: akses lewat Policy, jangan ditulis ke log.
 */
#[Fillable(['nama', 'pendidikan', 'pekerjaan', 'penghasilan', 'nomor_hp'])]
class OrangTuaWali extends Model
{
    /** @use HasFactory<OrangTuaWaliFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_orang_tua_wali';

    protected $primaryKey = 'id_orang_tua_wali';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pendidikan' => PendidikanOrangTua::class,
            'pekerjaan' => PekerjaanOrangTua::class,
            'penghasilan' => PenghasilanOrangTua::class,
        ];
    }

    /**
     * @return BelongsToMany<Murid, $this, MuridOrangTua>
     */
    public function murid(): BelongsToMany
    {
        return $this->belongsToMany(Murid::class, 'tb_murid_orang_tua', 'id_orang_tua_wali', 'id_murid', 'id_orang_tua_wali', 'id_murid')
            ->using(MuridOrangTua::class)
            ->withPivot('hubungan')
            ->withTimestamps();
    }
}
