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
        Schema::create('tb_satuan_pendidikan', function (Blueprint $table) {
            $table->id('id_satuan_pendidikan');
            $table->string('nama', 100);
            $table->string('bentuk_pendidikan', 10)->index();
            $table->char('npsn', 8)->nullable()->index();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_satuan_pendidikan');
    }
};
