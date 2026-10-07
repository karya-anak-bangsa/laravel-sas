<?php

namespace App\Http\Requests\Pengguna;

use App\Enums\KodePeran;
use App\Models\Pengguna;
use App\Models\TenagaKependidikan;
use App\Models\TenagaPendidik;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PenggunaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pengguna = $this->route('pengguna');

        return $pengguna
            ? $this->user()->can('update', $pengguna)
            : $this->user()->can('create', Pengguna::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'aktif' => $this->boolean('aktif'),
            'peran' => $this->input('peran', []),
        ]);
    }

    /**
     * Nama pengguna dan email adalah identitas login, jadi unik terhadap
     * semua akun termasuk yang sudah dihapus (sesuai unique index).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pengguna = $this->route('pengguna');

        return [
            'nama_pengguna' => [
                'required', 'string', 'regex:'.Pengguna::POLA_NAMA_PENGGUNA, 'max:50',
                Rule::unique(Pengguna::class, 'nama_pengguna')->ignore($pengguna, 'id_pengguna'),
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique(Pengguna::class, 'email')->ignore($pengguna, 'id_pengguna'),
            ],
            'password' => [$pengguna ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'aktif' => ['boolean'],
            'peran' => ['array'],
            'peran.*' => [Rule::in(array_map(fn (KodePeran $kode) => $kode->value, KodePeran::daftarTetap()))],
            'id_tenaga_pendidik' => [
                'nullable', 'integer',
                Rule::exists(TenagaPendidik::class, 'id_tenaga_pendidik')
                    ->withoutTrashed()
                    ->where(fn (Builder $query) => $query
                        ->whereNull('id_pengguna')
                        ->when($pengguna, fn (Builder $query) => $query->orWhere('id_pengguna', $pengguna->id_pengguna))),
            ],
            'id_tenaga_kependidikan' => [
                'nullable', 'integer',
                Rule::exists(TenagaKependidikan::class, 'id_tenaga_kependidikan')
                    ->withoutTrashed()
                    ->where(fn (Builder $query) => $query
                        ->whereNull('id_pengguna')
                        ->when($pengguna, fn (Builder $query) => $query->orWhere('id_pengguna', $pengguna->id_pengguna))),
            ],
        ];
    }

    /**
     * Administrator tidak boleh mengunci dirinya sendiri.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $pengguna = $this->route('pengguna');

                if (! $pengguna?->is($this->user())) {
                    return;
                }

                if (! $this->boolean('aktif')) {
                    $validator->errors()->add('aktif', 'Anda tidak dapat menonaktifkan akun sendiri.');
                }

                if (! in_array(KodePeran::Administrator->value, (array) $this->input('peran'), true)) {
                    $validator->errors()->add('peran', 'Anda tidak dapat mencabut peran Administrator dari akun sendiri.');
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
            'nama_pengguna' => 'nama pengguna',
            'peran.*' => 'peran',
            'id_tenaga_pendidik' => 'data tenaga pendidik',
            'id_tenaga_kependidikan' => 'data tenaga kependidikan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_pengguna.regex' => 'Nama pengguna hanya boleh berisi huruf, angka, titik, tanda hubung, dan garis bawah.',
            'id_tenaga_pendidik.exists' => 'Data tenaga pendidik tidak ditemukan atau sudah tertaut ke akun lain.',
            'id_tenaga_kependidikan.exists' => 'Data tenaga kependidikan tidak ditemukan atau sudah tertaut ke akun lain.',
        ];
    }
}
