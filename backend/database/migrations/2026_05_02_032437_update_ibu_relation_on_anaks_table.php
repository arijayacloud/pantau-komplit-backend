<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * UP
     */
    public function up(): void
    {
        Schema::table('anaks', function (Blueprint $table) {

            // 🔥 ubah ibu_id jadi nullable
            $table->unsignedBigInteger('ibu_id')->nullable()->change();

            // 🔥 tambah fallback nama ibu
            $table->string('nama_ibu')->nullable()->after('ibu_id');
        });
    }

    /**
     * DOWN (rollback)
     */
    public function down(): void
    {
        Schema::table('anaks', function (Blueprint $table) {

            // kembalikan jadi required
            $table->unsignedBigInteger('ibu_id')->nullable(false)->change();

            // hapus kolom tambahan
            $table->dropColumn('nama_ibu');
        });
    }
};
