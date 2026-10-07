<?php

namespace App\Http\Requests\MasterData;

use App\Enums\HubunganOrangTua;
use App\Models\MuridOrangTua;
use App\Models\OrangTuaWali;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Menautkan data orang tua/wali yang sudah ada ke seorang murid.
 */
class MuridOrangTuaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('murid'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_orang_tua_wali' => [
                'required', 'integer',
                Rule::exists(OrangTuaWali::class, 'id_orang_tua_wali')->withoutTrashed(),
            ],
            'hubungan' => ['required', Rule::enum(HubunganOrangTua::class)],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $tautan = MuridOrangTua::query()->where('id_murid', $this->route('murid')->id_murid);

                if ((clone $tautan)->where('hubungan', $this->input('hubungan'))->exists()) {
                    $validator->errors()->add('hubungan', 'Murid ini sudah memiliki '.strtolower(HubunganOrangTua::from($this->input('hubungan'))->label()).'.');
                }

                if ((clone $tautan)->where('id_orang_tua_wali', $this->integer('id_orang_tua_wali'))->exists()) {
                    $validator->errors()->add('id_orang_tua_wali', 'Orang tua/wali ini sudah tertaut ke murid tersebut.');
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
            'id_orang_tua_wali' => 'orang tua/wali',
        ];
    }
}
