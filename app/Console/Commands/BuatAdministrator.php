<?php

namespace App\Console\Commands;

use App\Actions\Pengguna\BuatPengguna;
use App\Enums\KodePeran;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('pengguna:buat-administrator')]
#[Description('Buat akun Administrator (dipakai saat instalasi awal)')]
class BuatAdministrator extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(BuatPengguna $buatPengguna): int
    {
        $data = [
            'nama_pengguna' => text('Nama pengguna', required: true),
            'email' => text('Email (opsional)') ?: null,
            'password' => password('Kata sandi', required: true),
            'password_confirmation' => password('Ulangi kata sandi', required: true),
        ];

        $validator = Validator::make($data, [
            'nama_pengguna' => ['required', 'alpha_dash', 'max:50', 'unique:tb_pengguna,nama_pengguna'],
            'email' => ['nullable', 'email', 'max:255', 'unique:tb_pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], attributes: [
            'nama_pengguna' => 'nama pengguna',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $pesan) {
                $this->components->error($pesan);
            }

            return self::FAILURE;
        }

        $pengguna = $buatPengguna->handle(
            $data['nama_pengguna'],
            $data['email'],
            $data['password'],
            KodePeran::Administrator,
        );

        $this->components->info("Akun Administrator \"{$pengguna->nama_pengguna}\" berhasil dibuat.");

        return self::SUCCESS;
    }
}
