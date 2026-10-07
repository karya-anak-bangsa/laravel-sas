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
        Schema::create('tb_konsentrasi_keahlian', function (Blueprint $table) {
            $table->id('id_konsentrasi_keahlian');
            $table->foreignId('id_program_keahlian')->constrained('tb_program_keahlian', 'id_program_keahlian')->restrictOnDelete();
            $table->string('nama', 150)->index();
            $table->string('singkatan', 10)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_konsentrasi_keahlian');
    }
};
