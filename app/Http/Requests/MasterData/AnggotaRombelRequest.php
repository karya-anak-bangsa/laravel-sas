<?php

namespace App\Http\Requests\MasterData;

use App\Models\Murid;
use App\Models\Rombel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AnggotaRombelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('rombel'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_murid' => ['required', 'array', 'min:1'],
            'id_murid.*' => [
                'integer', 'distinct',
                Rule::exists(Murid::class, 'id_murid')->withoutTrashed(),
            ],
        ];
    }

    /**
     * Satu murid hanya boleh berada di satu rombel pada tahun ajaran yang sama.
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

                /** @var Rombel $rombel */
                $rombel = $this->route('rombel');

                $sudahBerombel = Murid::query()
                    ->whereKey($this->input('id_murid'))
                    ->whereHas('rombel', fn (Builder $query) => $query->where('id_tahun_ajaran', $rombel->id_tahun_ajaran))
                    ->orderBy('nama_lengkap')
                    ->pluck('nama_lengkap');

                if ($sudahBerombel->isNotEmpty()) {
                    $validator->errors()->add('id_murid', 'Sudah memiliki rombel pada tahun ajaran ini: '.$sudahBerombel->join(', ').'.');
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
            'id_murid' => 'murid',
            'id_murid.*' => 'murid',
        ];
    }
}
