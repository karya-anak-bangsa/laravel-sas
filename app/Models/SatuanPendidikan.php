<?php

namespace App\Models;

use App\Enums\BentukPendidikan;
use Database\Factories\SatuanPendidikanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * @return HasMany<Rombel, $this>
     */
    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class, 'id_satuan_pendidikan', 'id_satuan_pendidikan');
    }

    /**
     * @return HasMany<TenagaPendidik, $this>
     */
    public function tenagaPendidik(): HasMany
    {
        return $this->hasMany(TenagaPendidik::class, 'id_satuan_pendidikan', 'id_satuan_pendidikan');
    }
}
