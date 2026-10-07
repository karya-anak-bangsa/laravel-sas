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
        // Sementara mengikuti data KTP; status dan aturan lain menunggu informasi yayasan (TBD).
        Schema::create('tb_tenaga_kependidikan', function (Blueprint $table) {
            $table->id('id_tenaga_kependidikan');
            $table->foreignId('id_pengguna')->nullable()->unique()->constrained('tb_pengguna', 'id_pengguna')->nullOnDelete();
            $table->char('nik', 16)->nullable()->index();
            $table->string('nama_lengkap', 150)->index();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->char('jenis_kelamin', 1);
            $table->string('alamat')->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kelurahan_desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('agama', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tenaga_kependidikan');
    }
};
