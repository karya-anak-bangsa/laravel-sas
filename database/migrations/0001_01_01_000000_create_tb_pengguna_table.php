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
        // Satu-satunya tabel akun untuk semua peran. Hanya data login,
        // data pribadi ada di tb_tenaga_pendidik / tb_tenaga_kependidikan.
        Schema::create('tb_pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama_pengguna', 50)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->boolean('aktif')->default(true)->index();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Kolom user_id dipakai langsung oleh session handler bawaan Laravel.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pengguna');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
