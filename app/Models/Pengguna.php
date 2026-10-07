<?php

namespace App\Models;

use App\Enums\KodePeran;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['nama_pengguna', 'email', 'password', 'aktif'])]
#[Hidden(['password', 'remember_token'])]
class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Pola nama pengguna: huruf, angka, titik, tanda hubung, dan garis bawah.
     */
    public const POLA_NAMA_PENGGUNA = '/^[A-Za-z0-9._-]+$/';

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
     * Peran tetap yang diberikan langsung ke akun (Administrator, Tenaga
     * Pendidik, Tenaga Kependidikan).
     *
     * @return BelongsToMany<Peran, $this>
     */
    public function peran(): BelongsToMany
    {
        return $this->belongsToMany(Peran::class, 'tb_pengguna_peran', 'id_pengguna', 'id_peran', 'id_pengguna', 'id_peran')
            ->withTimestamps()
            ->orderBy('tb_peran.id_peran');
    }

    /**
     * Data tenaga pendidik yang terhubung ke akun ini.
     *
     * @return HasOne<TenagaPendidik, $this>
     */
    public function tenagaPendidik(): HasOne
    {
        return $this->hasOne(TenagaPendidik::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Data tenaga kependidikan yang terhubung ke akun ini.
     *
     * @return HasOne<TenagaKependidikan, $this>
     */
    public function tenagaKependidikan(): HasOne
    {
        return $this->hasOne(TenagaKependidikan::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Penugasan (peran kontekstual) pada tahun ajaran aktif, lewat data tenaga pendidik.
     *
     * @return HasManyThrough<Penugasan, TenagaPendidik, $this>
     */
    public function penugasanAktif(): HasManyThrough
    {
        return $this->hasManyThrough(
            Penugasan::class, TenagaPendidik::class,
            'id_pengguna', 'id_tenaga_pendidik',
            'id_pengguna', 'id_tenaga_pendidik',
        )
            ->whereIn('tb_penugasan.id_tahun_ajaran', Semester::query()->aktif()->select('id_tahun_ajaran'))
            ->with('peran');
    }

    /**
     * Seluruh peran efektif: peran tetap + peran dari penugasan tahun ajaran aktif.
     *
     * @return Collection<int, KodePeran>
     */
    public function kodePeran(): Collection
    {
        return $this->peran->pluck('kode')
            ->merge($this->penugasanAktif->pluck('peran.kode'))
            ->unique(fn (KodePeran $kode) => $kode->value)
            ->sortBy(fn (KodePeran $kode) => array_search($kode, KodePeran::cases(), true))
            ->values();
    }

    /**
     * Apakah pengguna memiliki salah satu dari peran yang diberikan (termasuk peran dari penugasan).
     */
    public function memilikiPeran(KodePeran ...$kode): bool
    {
        return $this->kodePeran()->contains(fn (KodePeran $milik) => in_array($milik, $kode, true));
    }

    public function adalahAdministrator(): bool
    {
        return $this->memilikiPeran(KodePeran::Administrator);
    }
}
