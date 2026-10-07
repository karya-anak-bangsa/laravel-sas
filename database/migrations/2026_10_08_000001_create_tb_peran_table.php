<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_peran', function (Blueprint $table) {
            $table->id('id_peran');
            $table->string('kode', 50)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        // Data referensi peran diisi di sini agar selalu tersedia di setiap
        // lingkungan. Harus sama dengan App\Enums\KodePeran.
        $sekarang = now();

        DB::table('tb_peran')->insert(array_map(fn (array $peran) => [
            'kode' => $peran[0],
            'nama' => $peran[1],
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ], [
            ['administrator', 'Administrator'],
            ['kepala_sekolah', 'Kepala Sekolah'],
            ['wakil_kepala_sekolah', 'Wakil Kepala Sekolah'],
            ['ketua_jurusan', 'Ketua Jurusan'],
            ['wali_kelas', 'Wali Kelas'],
            ['tenaga_pendidik', 'Tenaga Pendidik'],
            ['tenaga_kependidikan', 'Tenaga Kependidikan'],
        ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_peran');
    }
};
