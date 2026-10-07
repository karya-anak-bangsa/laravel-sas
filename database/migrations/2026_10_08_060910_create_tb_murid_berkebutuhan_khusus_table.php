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
        // Satu murid dapat memiliki lebih dari satu kebutuhan khusus.
        // Murid tanpa baris di sini berarti "Tidak Ada".
        Schema::create('tb_murid_berkebutuhan_khusus', function (Blueprint $table) {
            $table->id('id_murid_berkebutuhan_khusus');
            $table->foreignId('id_murid')->constrained('tb_murid', 'id_murid')->cascadeOnDelete();
            $table->string('kode', 10);
            $table->timestamps();

            $table->unique(['id_murid', 'kode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_murid_berkebutuhan_khusus');
    }
};
