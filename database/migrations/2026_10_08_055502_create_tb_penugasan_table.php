<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Penugasan peran kontekstual per tahun ajaran. Kolom konteks yang terisi
        // bergantung pada perannya:
        //   Wali Kelas            → id_rombel
        //   Ketua Jurusan         → id_konsentrasi_keahlian
        //   Kepala Sekolah        → id_satuan_pendidikan
        //   Wakil Kepala Sekolah  → id_satuan_pendidikan + bidang
        Schema::create('tb_penugasan', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->foreignId('id_tenaga_pendidik')->constrained('tb_tenaga_pendidik', 'id_tenaga_pendidik')->restrictOnDelete();
            $table->foreignId('id_tahun_ajaran')->constrained('tb_tahun_ajaran', 'id_tahun_ajaran')->restrictOnDelete();
            $table->foreignId('id_peran')->constrained('tb_peran', 'id_peran')->restrictOnDelete();
            $table->foreignId('id_rombel')->nullable()->constrained('tb_rombel', 'id_rombel')->restrictOnDelete();
            $table->foreignId('id_konsentrasi_keahlian')->nullable()->constrained('tb_konsentrasi_keahlian', 'id_konsentrasi_keahlian')->restrictOnDelete();
            $table->foreignId('id_satuan_pendidikan')->nullable()->constrained('tb_satuan_pendidikan', 'id_satuan_pendidikan')->restrictOnDelete();
            $table->string('bidang', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['id_tahun_ajaran', 'id_peran']);
            $table->index(['id_tenaga_pendidik', 'id_tahun_ajaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penugasan');
    }
};
