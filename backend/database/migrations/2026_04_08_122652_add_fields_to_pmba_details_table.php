<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmba_details', function (Blueprint $table) {

            // 🔥 Tambahan field MPASI
            if (!Schema::hasColumn('pmba_details', 'kacang')) {
                $table->boolean('kacang')->default(false);
            }

            if (!Schema::hasColumn('pmba_details', 'susu')) {
                $table->boolean('susu')->default(false);
            }

            if (!Schema::hasColumn('pmba_details', 'telur')) {
                $table->boolean('telur')->default(false);
            }

            if (!Schema::hasColumn('pmba_details', 'vitamin_a')) {
                $table->boolean('vitamin_a')->default(false);
            }

            // 🔥 Optional (untuk AI / scoring)
            if (!Schema::hasColumn('pmba_details', 'skor')) {
                $table->integer('skor')->nullable();
            }

            if (!Schema::hasColumn('pmba_details', 'status')) {
                $table->enum('status', ['kurang', 'cukup', 'baik'])->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pmba_details', function (Blueprint $table) {

            $table->dropColumn([
                'kacang',
                'susu',
                'telur',
                'vitamin_a',
                'skor',
                'status'
            ]);
        });
    }
};
