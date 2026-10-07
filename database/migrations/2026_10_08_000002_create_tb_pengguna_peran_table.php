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
        Schema::create('tb_pengguna_peran', function (Blueprint $table) {
            $table->id('id_pengguna_peran');
            $table->foreignId('id_pengguna')->constrained('tb_pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->foreignId('id_peran')->constrained('tb_peran', 'id_peran')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['id_pengguna', 'id_peran']);
            $table->index('id_peran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pengguna_peran');
    }
};
