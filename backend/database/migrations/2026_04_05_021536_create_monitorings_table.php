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
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();

            // relasi
            $table->foreignId('kehamilan_id')
                ->constrained('kehamilans')
                ->cascadeOnDelete();

            // tanggal monitoring
            $table->date('tanggal');

            // 🔥 TTD (tetap dipertahankan)
            $table->boolean('bulan_1')->default(false);
            $table->boolean('bulan_2')->default(false);
            $table->boolean('bulan_3')->default(false);
            $table->boolean('bulan_4')->default(false);
            $table->boolean('bulan_5')->default(false);
            $table->boolean('bulan_6')->default(false);

            // 🔥 tambahan penting
            $table->integer('total_patum')->default(0); // jumlah bulan diminum
            $table->enum('status_kepatuhan', ['baik', 'cukup', 'kurang'])->nullable();

            // 🔥 status risiko
            $table->boolean('is_risk')->default(false);

            // 🔥 snapshot hasil AI / preview
            $table->json('hasil_konseling')->nullable();

            // 🔥 catatan manual kader
            $table->text('catatan_kader')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
