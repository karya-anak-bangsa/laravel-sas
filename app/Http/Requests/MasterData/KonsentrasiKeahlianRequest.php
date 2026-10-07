<?php

namespace App\Http\Requests\MasterData;

use App\Models\KonsentrasiKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KonsentrasiKeahlianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $konsentrasiKeahlian = $this->route('konsentrasiKeahlian');

        return $konsentrasiKeahlian
            ? $this->user()->can('update', $konsentrasiKeahlian)
            : $this->user()->can('create', KonsentrasiKeahlian::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('singkatan')) {
            $this->merge(['singkatan' => strtoupper(trim((string) $this->input('singkatan')))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $konsentrasiKeahlian = $this->route('konsentrasiKeahlian');

        return [
            'id_program_keahlian' => [
                'required', 'integer',
                Rule::exists(ProgramKeahlian::class, 'id_program_keahlian')->withoutTrashed(),
            ],
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique(KonsentrasiKeahlian::class, 'nama')
                    ->ignore($konsentrasiKeahlian, 'id_konsentrasi_keahlian')
                    ->withoutTrashed(),
            ],
            'singkatan' => [
                'required', 'string', 'max:10', 'alpha_num:ascii',
                Rule::unique(KonsentrasiKeahlian::class, 'singkatan')
                    ->ignore($konsentrasiKeahlian, 'id_konsentrasi_keahlian')
                    ->withoutTrashed(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_program_keahlian' => 'program keahlian',
            'nama' => 'nama konsentrasi keahlian',
        ];
    }
}
