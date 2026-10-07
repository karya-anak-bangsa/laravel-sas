<?php

namespace App\Http\Requests\MasterData;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Models\TenagaKependidikan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenagaKependidikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tenagaKependidikan = $this->route('tenagaKependidikan');

        return $tenagaKependidikan
            ? $this->user()->can('update', $tenagaKependidikan)
            : $this->user()->can('create', TenagaKependidikan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nik' => [
                'nullable', 'digits:16',
                Rule::unique(TenagaKependidikan::class, 'nik')
                    ->ignore($this->route('tenagaKependidikan'), 'id_tenaga_kependidikan')
                    ->withoutTrashed(),
            ],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::enum(JenisKelamin::class)],
            'alamat' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'digits_between:1,3'],
            'rw' => ['nullable', 'digits_between:1,3'],
            'kelurahan_desa' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'agama' => ['nullable', Rule::enum(Agama::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nik' => 'NIK',
            'nama_lengkap' => 'nama lengkap',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'rt' => 'RT',
            'rw' => 'RW',
            'kelurahan_desa' => 'kelurahan/desa',
        ];
    }
}
