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
        Schema::create('konselings', function (Blueprint $table) {
            $table->id();

            // 🔥 JUDUL (biar enak di admin)
            $table->string('judul')->nullable();

            // 🔥 RANGE KEHAMILAN (minggu)
            $table->unsignedInteger('min_minggu')->nullable();
            $table->unsignedInteger('max_minggu')->nullable();

            // 🔥 RANGE ANAK (bulan)
            $table->unsignedInteger('min_bulan')->nullable();
            $table->unsignedInteger('max_bulan')->nullable();

            // 🔥 ISI KONSELING
            $table->text('materi');

            // 🔥 KATEGORI
            $table->enum('kategori', ['kehamilan', 'anak'])->default('kehamilan');

            // 🔥 RESIKO
            $table->enum('resiko', ['normal', 'tinggi'])->default('normal');

            // 🔥 PRIORITAS (penting untuk sorting)
            $table->integer('priority')->default(1);

            $table->timestamps();

            // 🔥 INDEX BIAR CEPAT
            $table->index(['kategori', 'resiko']);
            $table->index(['min_minggu', 'max_minggu']);
            $table->index(['min_bulan', 'max_bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konselings');
    }
};
