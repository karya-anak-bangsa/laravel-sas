<?php

namespace App\Http\Requests\MasterData;

use App\Enums\Agama;
use App\Enums\BerkebutuhanKhusus;
use App\Enums\JenisKelamin;
use App\Enums\ModaTransportasi;
use App\Enums\TempatTinggal;
use App\Models\Murid;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MuridRequest extends FormRequest
{
    /**
     * Pola nomor HP Indonesia: 08…, 628…, atau +628….
     */
    public const POLA_NOMOR_HP = '/^(\+62|62|0)8[0-9]{7,12}$/';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $murid = $this->route('murid');

        return $murid
            ? $this->user()->can('update', $murid)
            : $this->user()->can('create', Murid::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nomor_hp' => $this->filled('nomor_hp')
                ? preg_replace('/[\s\-().]/', '', (string) $this->input('nomor_hp'))
                : null,
            'berkebutuhan_khusus' => $this->input('berkebutuhan_khusus', []),
        ]);
    }

    /**
     * Isian wajib minimal: nama lengkap, jenis kelamin, tanggal lahir.
     * Nomor identitas opsional, tetapi divalidasi digit dan keunikannya bila diisi.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $murid = $this->route('murid');

        return [
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', Rule::enum(JenisKelamin::class)],
            'nisn' => [
                'nullable', 'digits:10',
                Rule::unique(Murid::class, 'nisn')->ignore($murid, 'id_murid')->withoutTrashed(),
            ],
            'nik' => [
                'nullable', 'digits:16',
                Rule::unique(Murid::class, 'nik')->ignore($murid, 'id_murid')->withoutTrashed(),
            ],
            // No. KK boleh sama: murid bersaudara berada dalam satu KK.
            'no_kk' => ['nullable', 'digits:16'],
            'no_seri_ijazah' => ['nullable', 'string', 'max:50'],
            'no_seri_skhus' => ['nullable', 'string', 'max:50'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'agama' => ['nullable', Rule::enum(Agama::class)],
            'berkebutuhan_khusus' => ['array'],
            'berkebutuhan_khusus.*' => ['distinct', Rule::enum(BerkebutuhanKhusus::class)],
            'alamat_jalan' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'digits_between:1,3'],
            'rw' => ['nullable', 'digits_between:1,3'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'kelurahan_desa' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'digits:5'],
            'moda_transportasi' => ['nullable', Rule::enum(ModaTransportasi::class)],
            'tempat_tinggal' => ['nullable', Rule::enum(TempatTinggal::class)],
            'nomor_hp' => ['nullable', 'regex:'.self::POLA_NOMOR_HP],
            'email' => ['nullable', 'email', 'max:255'],
            'no_kps_pkh' => ['nullable', 'string', 'max:50'],
            'no_kip' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nomor_hp.regex' => 'Nomor HP harus nomor ponsel Indonesia yang valid, mis. 081234567890.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'nama lengkap',
            'jenis_kelamin' => 'jenis kelamin',
            'nisn' => 'NISN',
            'nik' => 'NIK',
            'no_kk' => 'nomor KK',
            'no_seri_ijazah' => 'nomor seri ijazah',
            'no_seri_skhus' => 'nomor seri SKHUS',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'berkebutuhan_khusus.*' => 'berkebutuhan khusus',
            'alamat_jalan' => 'alamat jalan',
            'rt' => 'RT',
            'rw' => 'RW',
            'kelurahan_desa' => 'kelurahan/desa',
            'kode_pos' => 'kode pos',
            'moda_transportasi' => 'moda transportasi',
            'tempat_tinggal' => 'tempat tinggal',
            'nomor_hp' => 'nomor HP',
            'no_kps_pkh' => 'nomor KPS/PKH',
            'no_kip' => 'nomor KIP',
        ];
    }
}
