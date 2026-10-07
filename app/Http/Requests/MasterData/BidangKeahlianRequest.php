<?php

namespace App\Http\Requests\MasterData;

use App\Models\BidangKeahlian;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BidangKeahlianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $bidangKeahlian = $this->route('bidangKeahlian');

        return $bidangKeahlian
            ? $this->user()->can('update', $bidangKeahlian)
            : $this->user()->can('create', BidangKeahlian::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique(BidangKeahlian::class, 'nama')
                    ->ignore($this->route('bidangKeahlian'), 'id_bidang_keahlian')
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
            'nama' => 'nama bidang keahlian',
        ];
    }
}
