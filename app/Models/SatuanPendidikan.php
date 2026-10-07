<?php

namespace App\Models;

use App\Enums\BentukPendidikan;
use Database\Factories\SatuanPendidikanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama', 'bentuk_pendidikan', 'npsn', 'alamat'])]
class SatuanPendidikan extends Model
{
    /** @use HasFactory<SatuanPendidikanFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_satuan_pendidikan';

    protected $primaryKey = 'id_satuan_pendidikan';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bentuk_pendidikan' => BentukPendidikan::class,
        ];
    }
}
