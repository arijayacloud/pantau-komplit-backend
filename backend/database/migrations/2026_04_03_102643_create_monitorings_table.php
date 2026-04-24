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

            // relasi ke kehamilan
            $table->foreignId('kehamilan_id')->constrained('kehamilans')->cascadeOnDelete();

            // data utama
            $table->date('tanggal');

            // 🔥 monitoring bulanan
            $table->boolean('bulan_1')->default(false);
            $table->boolean('bulan_2')->default(false);
            $table->boolean('bulan_3')->default(false);
            $table->boolean('bulan_4')->default(false);
            $table->boolean('bulan_5')->default(false);
            $table->boolean('bulan_6')->default(false);
            $table->text('catatan_konseling')->nullable();

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
