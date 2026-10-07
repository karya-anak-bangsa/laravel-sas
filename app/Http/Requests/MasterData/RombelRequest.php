<?php

namespace App\Http\Requests\MasterData;

use App\Enums\Tingkat;
use App\Models\KonsentrasiKeahlian;
use App\Models\Rombel;
use App\Models\SatuanPendidikan;
use App\Models\TahunAjaran;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RombelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $rombel = $this->route('rombel');

        return $rombel
            ? $this->user()->can('update', $rombel)
            : $this->user()->can('create', Rombel::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_tahun_ajaran' => [
                'required', 'integer',
                Rule::exists(TahunAjaran::class, 'id_tahun_ajaran')->withoutTrashed(),
            ],
            'id_satuan_pendidikan' => [
                'required', 'integer',
                Rule::exists(SatuanPendidikan::class, 'id_satuan_pendidikan')->withoutTrashed(),
            ],
            'tingkat' => ['required', Rule::enum(Tingkat::class)],
            'id_konsentrasi_keahlian' => [
                'nullable', 'integer',
                Rule::exists(KonsentrasiKeahlian::class, 'id_konsentrasi_keahlian')->withoutTrashed(),
            ],
            'nama' => [
                'required', 'string', 'max:50',
                Rule::unique(Rombel::class, 'nama')
                    ->where('id_tahun_ajaran', $this->integer('id_tahun_ajaran'))
                    ->where('id_satuan_pendidikan', $this->integer('id_satuan_pendidikan'))
                    ->ignore($this->route('rombel'), 'id_rombel')
                    ->withoutTrashed(),
            ],
        ];
    }

    /**
     * Aturan yang bergantung pada bentuk pendidikan satuan pendidikan terpilih.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['id_satuan_pendidikan', 'tingkat', 'id_konsentrasi_keahlian'])) {
                    return;
                }

                $bentuk = SatuanPendidikan::query()->findOrFail($this->integer('id_satuan_pendidikan'))->bentuk_pendidikan;
                $tingkat = Tingkat::from($this->integer('tingkat'));

                if (! in_array($tingkat, $bentuk->tingkat(), true)) {
                    $validator->errors()->add('tingkat', "Tingkat {$tingkat->label()} tidak tersedia di {$bentuk->label()}.");
                }

                if ($bentuk->memilikiKonsentrasiKeahlian() && $this->isNotFilled('id_konsentrasi_keahlian')) {
                    $validator->errors()->add('id_konsentrasi_keahlian', "Konsentrasi keahlian wajib diisi untuk rombel {$bentuk->label()}.");
                }

                if (! $bentuk->memilikiKonsentrasiKeahlian() && $this->filled('id_konsentrasi_keahlian')) {
                    $validator->errors()->add('id_konsentrasi_keahlian', "Rombel {$bentuk->label()} tidak memiliki konsentrasi keahlian.");
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
            'id_satuan_pendidikan' => 'satuan pendidikan',
            'id_konsentrasi_keahlian' => 'konsentrasi keahlian',
            'nama' => 'nama rombel',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.unique' => 'Nama rombel sudah dipakai di satuan pendidikan dan tahun ajaran yang sama.',
        ];
    }
}
