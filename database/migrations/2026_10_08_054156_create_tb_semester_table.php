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
        // Setiap tahun ajaran memiliki tepat dua semester (ganjil dan genap),
        // dibuat dan diubah bersama tahun ajarannya.
        Schema::create('tb_semester', function (Blueprint $table) {
            $table->id('id_semester');
            $table->foreignId('id_tahun_ajaran')->constrained('tb_tahun_ajaran', 'id_tahun_ajaran')->restrictOnDelete();
            $table->string('jenis', 10);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('aktif')->default(false)->index();
            $table->timestamps();

            $table->unique(['id_tahun_ajaran', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_semester');
    }
};
