<?php

namespace App\Models;

use App\Enums\KodePeran;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama_pengguna', 'email', 'password', 'aktif'])]
#[Hidden(['password', 'remember_token'])]
class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tb_pengguna';

    protected $primaryKey = 'id_pengguna';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Peran, $this>
     */
    public function peran(): BelongsToMany
    {
        return $this->belongsToMany(Peran::class, 'tb_pengguna_peran', 'id_pengguna', 'id_peran', 'id_pengguna', 'id_peran')
            ->withTimestamps();
    }

    /**
     * Apakah pengguna memiliki salah satu dari peran yang diberikan.
     */
    public function memilikiPeran(KodePeran ...$kode): bool
    {
        return $this->peran->contains(fn (Peran $peran) => in_array($peran->kode, $kode, true));
    }

    public function adalahAdministrator(): bool
    {
        return $this->memilikiPeran(KodePeran::Administrator);
    }
}
