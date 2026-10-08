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
        // Anggota rombel. Satu murid hanya satu rombel per tahun ajaran
        // (rombel sudah terikat tahun ajaran; aturan ini divalidasi di aplikasi).
        Schema::create('tb_rombel_murid', function (Blueprint $table) {
            $table->id('id_rombel_murid');
            $table->foreignId('id_rombel')->constrained('tb_rombel', 'id_rombel')->restrictOnDelete();
            $table->foreignId('id_murid')->constrained('tb_murid', 'id_murid')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['id_rombel', 'id_murid']);
            $table->index('id_murid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_rombel_murid');
    }
};
