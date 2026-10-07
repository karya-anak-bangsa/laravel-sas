<?php

namespace App\Models;

use App\Enums\HubunganOrangTua;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Relasi murid ↔ orang tua/wali beserta hubungannya.
 */
class MuridOrangTua extends Pivot
{
    protected $table = 'tb_murid_orang_tua';

    protected $primaryKey = 'id_murid_orang_tua';

    public $incrementing = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hubungan' => HubunganOrangTua::class,
        ];
    }
}
