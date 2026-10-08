<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Login hanya memakai email + password: nama pengguna dihapus dan email wajib.
     */
    public function up(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table) {
            $table->dropUnique(['nama_pengguna']);
            $table->dropColumn('nama_pengguna');
            $table->string('email')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table) {
            $table->string('nama_pengguna', 50)->nullable()->unique()->after('id_pengguna');
            $table->string('email')->nullable()->change();
        });
    }
};
