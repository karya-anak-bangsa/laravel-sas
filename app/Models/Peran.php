<?php

namespace App\Models;

use App\Enums\KodePeran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Peran extends Model
{
    protected $table = 'tb_peran';

    protected $primaryKey = 'id_peran';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kode' => KodePeran::class,
        ];
    }

    /**
     * @return BelongsToMany<Pengguna, $this>
     */
    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'tb_pengguna_peran', 'id_peran', 'id_pengguna', 'id_peran', 'id_pengguna')
            ->withTimestamps();
    }
}
