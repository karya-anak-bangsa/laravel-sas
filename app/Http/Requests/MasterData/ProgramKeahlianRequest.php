<?php

namespace App\Http\Requests\MasterData;

use App\Models\BidangKeahlian;
use App\Models\ProgramKeahlian;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramKeahlianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $programKeahlian = $this->route('programKeahlian');

        return $programKeahlian
            ? $this->user()->can('update', $programKeahlian)
            : $this->user()->can('create', ProgramKeahlian::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_bidang_keahlian' => [
                'required', 'integer',
                Rule::exists(BidangKeahlian::class, 'id_bidang_keahlian')->withoutTrashed(),
            ],
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique(ProgramKeahlian::class, 'nama')
                    ->ignore($this->route('programKeahlian'), 'id_program_keahlian')
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
            'id_bidang_keahlian' => 'bidang keahlian',
            'nama' => 'nama program keahlian',
        ];
    }
}
