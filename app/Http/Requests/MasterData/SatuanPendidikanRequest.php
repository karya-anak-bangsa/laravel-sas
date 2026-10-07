<?php

namespace App\Http\Requests\MasterData;

use App\Enums\BentukPendidikan;
use App\Models\SatuanPendidikan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SatuanPendidikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $satuanPendidikan = $this->route('satuanPendidikan');

        return $satuanPendidikan
            ? $this->user()->can('update', $satuanPendidikan)
            : $this->user()->can('create', SatuanPendidikan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'bentuk_pendidikan' => ['required', Rule::enum(BentukPendidikan::class)],
            'npsn' => [
                'nullable', 'digits:8',
                Rule::unique(SatuanPendidikan::class, 'npsn')
                    ->ignore($this->route('satuanPendidikan'), 'id_satuan_pendidikan')
                    ->withoutTrashed(),
            ],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama satuan pendidikan',
            'bentuk_pendidikan' => 'bentuk pendidikan',
            'npsn' => 'NPSN',
        ];
    }
}
