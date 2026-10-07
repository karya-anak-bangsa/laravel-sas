<?php

namespace App\Http\Requests\MasterData;

use App\Models\TahunAjaran;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TahunAjaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tahunAjaran = $this->route('tahunAjaran');

        return $tahunAjaran
            ? $this->user()->can('update', $tahunAjaran)
            : $this->user()->can('create', TahunAjaran::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|Closure|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => [
                'bail', 'required', 'string', 'regex:/^\d{4}\/\d{4}$/',
                function (string $attribute, mixed $value, Closure $fail) {
                    [$awal, $akhir] = array_map('intval', explode('/', (string) $value) + [1 => 0]);

                    if ($akhir !== $awal + 1) {
                        $fail('Tahun kedua pada :attribute harus satu tahun setelah tahun pertama, mis. 2026/2027.');
                    }
                },
                Rule::unique(TahunAjaran::class, 'nama')
                    ->ignore($this->route('tahunAjaran'), 'id_tahun_ajaran')
                    ->withoutTrashed(),
            ],
            'ganjil_mulai' => ['required', 'date'],
            'ganjil_selesai' => ['required', 'date', 'after:ganjil_mulai'],
            'genap_mulai' => ['required', 'date', 'after:ganjil_selesai'],
            'genap_selesai' => ['required', 'date', 'after:genap_mulai'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.regex' => 'Format :attribute harus TTTT/TTTT, mis. 2026/2027.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'tahun ajaran',
            'ganjil_mulai' => 'tanggal mulai semester ganjil',
            'ganjil_selesai' => 'tanggal selesai semester ganjil',
            'genap_mulai' => 'tanggal mulai semester genap',
            'genap_selesai' => 'tanggal selesai semester genap',
        ];
    }
}
