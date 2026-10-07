<?php

namespace App\Http\Requests\MasterData;

use App\Enums\KodePeran;
use App\Models\KonsentrasiKeahlian;
use App\Models\Penugasan;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PenugasanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $penugasan = $this->route('penugasan');

        return $penugasan
            ? $this->user()->can('update', $penugasan)
            : $this->user()->can('create', Penugasan::class);
    }

    /**
     * Kolom konteks yang tidak relevan dengan peran terpilih dikecualikan
     * (exclude_unless) sehingga tersimpan sebagai null.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $wali = KodePeran::WaliKelas->value;
        $ketua = KodePeran::KetuaJurusan->value;
        $kepala = KodePeran::KepalaSekolah->value;
        $wakil = KodePeran::WakilKepalaSekolah->value;

        return [
            'id_tahun_ajaran' => [
                'required', 'integer',
                Rule::exists(TahunAjaran::class, 'id_tahun_ajaran')->withoutTrashed(),
            ],
            'id_tenaga_pendidik' => [
                'required', 'integer',
                Rule::exists(TenagaPendidik::class, 'id_tenaga_pendidik')->withoutTrashed(),
            ],
            'kode_peran' => [
                'required',
                Rule::in(array_map(fn (KodePeran $kode) => $kode->value, KodePeran::daftarKontekstual())),
            ],
            'id_rombel' => [
                "exclude_unless:kode_peran,{$wali}", 'required', 'integer',
                Rule::exists(Rombel::class, 'id_rombel')
                    ->where('id_tahun_ajaran', $this->integer('id_tahun_ajaran'))
                    ->withoutTrashed(),
            ],
            'id_konsentrasi_keahlian' => [
                "exclude_unless:kode_peran,{$ketua}", 'required', 'integer',
                Rule::exists(KonsentrasiKeahlian::class, 'id_konsentrasi_keahlian')->withoutTrashed(),
            ],
            'id_satuan_pendidikan' => [
                "exclude_unless:kode_peran,{$kepala},{$wakil}", 'required', 'integer',
                Rule::exists(SatuanPendidikan::class, 'id_satuan_pendidikan')->withoutTrashed(),
            ],
            'bidang' => ["exclude_unless:kode_peran,{$wakil}", 'required', 'string', 'max:100'],
        ];
    }

    /**
     * Batasan jumlah pemegang peran per konteks.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $data = $validator->validated();
                $kode = KodePeran::from($data['kode_peran']);

                // [kolom pembeda, isian yang diberi pesan, pesan galat]
                [$kolom, $isian, $pesan] = match ($kode) {
                    KodePeran::WaliKelas => [
                        ['id_rombel' => $data['id_rombel']],
                        'id_rombel', 'Rombel ini sudah memiliki wali kelas pada tahun ajaran tersebut.',
                    ],
                    KodePeran::KetuaJurusan => [
                        ['id_konsentrasi_keahlian' => $data['id_konsentrasi_keahlian']],
                        'id_konsentrasi_keahlian', 'Konsentrasi keahlian ini sudah memiliki ketua jurusan pada tahun ajaran tersebut.',
                    ],
                    KodePeran::KepalaSekolah => [
                        ['id_satuan_pendidikan' => $data['id_satuan_pendidikan']],
                        'id_satuan_pendidikan', 'Satuan pendidikan ini sudah memiliki kepala sekolah pada tahun ajaran tersebut.',
                    ],
                    KodePeran::WakilKepalaSekolah => [
                        [
                            'id_satuan_pendidikan' => $data['id_satuan_pendidikan'],
                            'id_tenaga_pendidik' => $data['id_tenaga_pendidik'],
                            'bidang' => $data['bidang'],
                        ],
                        'bidang', 'Penugasan wakil kepala sekolah dengan bidang ini sudah ada.',
                    ],
                };

                $sudahAda = Penugasan::query()
                    ->where('id_tahun_ajaran', $data['id_tahun_ajaran'])
                    ->whereHas('peran', fn (Builder $query) => $query->where('kode', $kode))
                    ->where($kolom)
                    ->when($this->route('penugasan'), fn (Builder $query, Penugasan $penugasan) => $query->whereKeyNot($penugasan->getKey()))
                    ->exists();

                if ($sudahAda) {
                    $validator->errors()->add($isian, $pesan);
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_tahun_ajaran' => 'tahun ajaran',
            'id_tenaga_pendidik' => 'tenaga pendidik',
            'kode_peran' => 'peran',
            'id_rombel' => 'rombel',
            'id_konsentrasi_keahlian' => 'konsentrasi keahlian',
            'id_satuan_pendidikan' => 'satuan pendidikan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_rombel.exists' => 'Rombel harus berada pada tahun ajaran yang dipilih.',
        ];
    }
}
