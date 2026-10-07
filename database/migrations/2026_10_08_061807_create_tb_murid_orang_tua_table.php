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
        Schema::create('tb_murid_orang_tua', function (Blueprint $table) {
            $table->id('id_murid_orang_tua');
            $table->foreignId('id_murid')->constrained('tb_murid', 'id_murid')->cascadeOnDelete();
            $table->foreignId('id_orang_tua_wali')->constrained('tb_orang_tua_wali', 'id_orang_tua_wali')->restrictOnDelete();
            $table->string('hubungan', 20);
            $table->timestamps();

            // Satu murid: paling banyak satu ayah kandung, satu ibu kandung, satu wali.
            $table->unique(['id_murid', 'hubungan']);
            $table->unique(['id_murid', 'id_orang_tua_wali']);
            $table->index('id_orang_tua_wali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_murid_orang_tua');
    }
};
