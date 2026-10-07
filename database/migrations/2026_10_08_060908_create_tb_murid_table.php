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
        Schema::create('tb_murid', function (Blueprint $table) {
            $table->id('id_murid');
            $table->string('nama_lengkap', 150)->index();
            $table->char('jenis_kelamin', 1);
            $table->char('nisn', 10)->nullable()->index();
            $table->char('nik', 16)->nullable()->index();
            $table->char('no_kk', 16)->nullable();
            $table->string('no_seri_ijazah', 50)->nullable();
            $table->string('no_seri_skhus', 50)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir');
            $table->string('agama', 10)->nullable();

            // Alamat (rincian mengikuti Dapodik).
            $table->string('alamat_jalan')->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('kelurahan_desa', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->char('kode_pos', 5)->nullable();

            $table->string('moda_transportasi', 20)->nullable();
            $table->string('tempat_tinggal', 20)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('no_kps_pkh', 50)->nullable();
            $table->string('no_kip', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_murid');
    }
};
