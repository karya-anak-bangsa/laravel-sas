<?php

namespace App\Models;

use App\Enums\JenisSemester;
use Database\Factories\TahunAjaranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama'])]
class TahunAjaran extends Model
{
    /** @use HasFactory<TahunAjaranFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tb_tahun_ajaran';

    protected $primaryKey = 'id_tahun_ajaran';

    /**
     * @return HasMany<Semester, $this>
     */
    public function semester(): HasMany
    {
        return $this->hasMany(Semester::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    /**
     * @return HasMany<Rombel, $this>
     */
    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    /**
     * @return HasOne<Semester, $this>
     */
    public function semesterGanjil(): HasOne
    {
        return $this->hasOne(Semester::class, 'id_tahun_ajaran', 'id_tahun_ajaran')
            ->where('jenis', JenisSemester::Ganjil);
    }

    /**
     * @return HasOne<Semester, $this>
     */
    public function semesterGenap(): HasOne
    {
        return $this->hasOne(Semester::class, 'id_tahun_ajaran', 'id_tahun_ajaran')
            ->where('jenis', JenisSemester::Genap);
    }
}
