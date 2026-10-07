<?php

namespace App\Http\Requests\MasterData;

use App\Enums\HubunganOrangTua;
use App\Enums\PekerjaanOrangTua;
use App\Enums\PendidikanOrangTua;
use App\Enums\PenghasilanOrangTua;
use App\Models\Murid;
use App\Models\MuridOrangTua;
use App\Models\OrangTuaWali;
use App\Rules\NomorPonselIndonesia;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrangTuaWaliRequest extends FormRequest
{
    /**
     * Saat membuat data baru dari halaman murid (id_murid terisi), pengguna
     * juga harus boleh mengubah murid tersebut.
     */
    public function authorize(): bool
    {
        $orangTuaWali = $this->route('orangTuaWali');

        if ($orangTuaWali) {
            return $this->user()->can('update', $orangTuaWali);
        }

        $murid = $this->filled('id_murid') ? Murid::query()->find($this->integer('id_murid')) : null;

        return $this->user()->can('create', OrangTuaWali::class)
            && (! $murid || $this->user()->can('update', $murid));
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['nomor_hp' => NomorPonselIndonesia::normalkan($this->input('nomor_hp'))]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'pendidikan' => ['nullable', Rule::enum(PendidikanOrangTua::class)],
            'pekerjaan' => ['nullable', Rule::enum(PekerjaanOrangTua::class)],
            'penghasilan' => ['nullable', Rule::enum(PenghasilanOrangTua::class)],
            'nomor_hp' => ['nullable', new NomorPonselIndonesia],
            // Opsional: langsung tautkan ke murid (hanya saat membuat data baru).
            'id_murid' => [
                Rule::excludeIf((bool) $this->route('orangTuaWali')), 'nullable', 'integer',
                Rule::exists(Murid::class, 'id_murid')->withoutTrashed(),
            ],
            'hubungan' => ['exclude_without:id_murid', 'required', Rule::enum(HubunganOrangTua::class)],
        ];
    }

    /**
     * Hubungan yang sama tidak boleh dipakai dua kali pada satu murid.
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

                if (empty($data['id_murid'])) {
                    return;
                }

                $sudahAda = MuridOrangTua::query()
                    ->where('id_murid', $data['id_murid'])
                    ->where('hubungan', $data['hubungan'])
                    ->exists();

                if ($sudahAda) {
                    $validator->errors()->add('hubungan', 'Murid ini sudah memiliki '.strtolower(HubunganOrangTua::from($data['hubungan'])->label()).'.');
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
            'nama' => 'nama orang tua/wali',
            'nomor_hp' => 'nomor HP',
        ];
    }
}
