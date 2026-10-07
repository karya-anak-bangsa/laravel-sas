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
        Schema::create('tb_program_keahlian', function (Blueprint $table) {
            $table->id('id_program_keahlian');
            $table->foreignId('id_bidang_keahlian')->constrained('tb_bidang_keahlian', 'id_bidang_keahlian')->restrictOnDelete();
            $table->string('nama', 150)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_program_keahlian');
    }
};
