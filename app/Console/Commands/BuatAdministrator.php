<?php

namespace App\Console\Commands;

use App\Actions\Pengguna\BuatPengguna;
use App\Enums\KodePeran;
use App\Models\Pengguna;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'email' => text('Email', required: true),
            'password' => password('Kata sandi', required: true),
            'password_confirmation' => password('Ulangi kata sandi', required: true),
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email', 'max:255', Rule::unique(Pengguna::class, 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $pesan) {
                $this->components->error($pesan);
            }

            return self::FAILURE;
        }

        $pengguna = $buatPengguna->handle(
            $data['email'],
            $data['password'],
            KodePeran::Administrator,
        );

        $this->components->info("Akun Administrator \"{$pengguna->email}\" berhasil dibuat.");

        return self::SUCCESS;
    }
}
