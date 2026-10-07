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
        Schema::create('tb_tenaga_pendidik', function (Blueprint $table) {
            $table->id('id_tenaga_pendidik');
            // Akun login opsional: data dapat diinput sebelum akunnya dibuat.
            $table->foreignId('id_pengguna')->nullable()->unique()->constrained('tb_pengguna', 'id_pengguna')->nullOnDelete();
            $table->foreignId('id_satuan_pendidikan')->constrained('tb_satuan_pendidikan', 'id_satuan_pendidikan')->restrictOnDelete();
            $table->string('nama_lengkap', 150)->index();
            $table->char('nuptk', 16)->nullable()->index();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan_terakhir', 10);
            $table->string('status', 10)->index();
            $table->date('tmt_gtt')->nullable();
            $table->date('tmt_gty')->nullable();
            // Masa kerja diisi manual, tidak dihitung dari TMT.
            $table->unsignedTinyInteger('masa_kerja_tahun')->default(0);
            $table->unsignedTinyInteger('masa_kerja_bulan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tenaga_pendidik');
    }
};
