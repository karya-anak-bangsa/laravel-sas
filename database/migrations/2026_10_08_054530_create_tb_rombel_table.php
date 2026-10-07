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
        Schema::create('tb_rombel', function (Blueprint $table) {
            $table->id('id_rombel');
            $table->foreignId('id_tahun_ajaran')->constrained('tb_tahun_ajaran', 'id_tahun_ajaran')->restrictOnDelete();
            $table->foreignId('id_satuan_pendidikan')->constrained('tb_satuan_pendidikan', 'id_satuan_pendidikan')->restrictOnDelete();
            $table->foreignId('id_konsentrasi_keahlian')->nullable()->constrained('tb_konsentrasi_keahlian', 'id_konsentrasi_keahlian')->restrictOnDelete();
            $table->unsignedTinyInteger('tingkat');
            $table->string('nama', 50);
            $table->timestamps();
            $table->softDeletes();

            // Daftar rombel disaring per tahun ajaran + satuan pendidikan, diurutkan per tingkat.
            $table->index(['id_tahun_ajaran', 'id_satuan_pendidikan', 'tingkat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rombel');
    }
};
