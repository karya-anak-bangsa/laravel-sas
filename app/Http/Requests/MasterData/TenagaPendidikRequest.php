<?php

namespace App\Http\Requests\MasterData;

use App\Enums\JenjangPendidikan;
use App\Enums\StatusTenagaPendidik;
use App\Models\SatuanPendidikan;
use App\Models\TenagaPendidik;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenagaPendidikRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tenagaPendidik = $this->route('tenagaPendidik');

        return $tenagaPendidik
            ? $this->user()->can('update', $tenagaPendidik)
            : $this->user()->can('create', TenagaPendidik::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nuptk' => [
                'nullable', 'digits:16',
                Rule::unique(TenagaPendidik::class, 'nuptk')
                    ->ignore($this->route('tenagaPendidik'), 'id_tenaga_pendidik')
                    ->withoutTrashed(),
            ],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'pendidikan_terakhir' => ['required', Rule::enum(JenjangPendidikan::class)],
            'id_satuan_pendidikan' => [
                'required', 'integer',
                Rule::exists(SatuanPendidikan::class, 'id_satuan_pendidikan')->withoutTrashed(),
            ],
            'status' => ['required', Rule::enum(StatusTenagaPendidik::class)],
            'tmt_gtt' => ['nullable', 'date'],
            'tmt_gty' => ['nullable', 'date'],
            'masa_kerja_tahun' => ['required', 'integer', 'min:0', 'max:60'],
            'masa_kerja_bulan' => ['required', 'integer', 'min:0', 'max:11'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'nama lengkap',
            'nuptk' => 'NUPTK',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'id_satuan_pendidikan' => 'satuan pendidikan',
            'tmt_gtt' => 'TMT GTT',
            'tmt_gty' => 'TMT GTY',
            'masa_kerja_tahun' => 'masa kerja (tahun)',
            'masa_kerja_bulan' => 'masa kerja (bulan)',
        ];
    }
}
