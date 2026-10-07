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
        // Terpisah dari murid agar murid bersaudara dapat memakai data yang sama.
        Schema::create('tb_orang_tua_wali', function (Blueprint $table) {
            $table->id('id_orang_tua_wali');
            $table->string('nama', 150)->index();
            $table->string('pendidikan', 20)->nullable();
            $table->string('pekerjaan', 30)->nullable();
            $table->string('penghasilan', 30)->nullable();
            $table->string('nomor_hp', 20)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_orang_tua_wali');
    }
};
