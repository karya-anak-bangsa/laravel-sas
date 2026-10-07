<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class KonfigurasiTest extends TestCase
{
    public function test_locale_dan_zona_waktu_sesuai_indonesia(): void
    {
        $this->assertSame('id', app()->getLocale());
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
    }

    public function test_pesan_validasi_berbahasa_indonesia(): void
    {
        $validator = Validator::make(['nama' => ''], ['nama' => 'required']);

        $this->assertSame('Nama wajib diisi.', $validator->errors()->first('nama'));
    }
}
