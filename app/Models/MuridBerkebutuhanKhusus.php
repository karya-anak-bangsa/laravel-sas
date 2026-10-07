<?php

namespace App\Models;

use App\Enums\BerkebutuhanKhusus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_murid', 'kode'])]
class MuridBerkebutuhanKhusus extends Model
{
    protected $table = 'tb_murid_berkebutuhan_khusus';

    protected $primaryKey = 'id_murid_berkebutuhan_khusus';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kode' => BerkebutuhanKhusus::class,
        ];
    }

    /**
     * @return BelongsTo<Murid, $this>
     */
    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class, 'id_murid', 'id_murid');
    }
}
