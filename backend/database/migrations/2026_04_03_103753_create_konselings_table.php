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

            // 🔥 berdasarkan umur
            $table->integer('min_minggu')->nullable();
            $table->integer('max_minggu')->nullable();

            // atau berdasarkan bulan anak
            $table->integer('min_bulan')->nullable();
            $table->integer('max_bulan')->nullable();

            $table->text('materi');
            $table->enum('kategori', ['kehamilan', 'anak'])->default('kehamilan');
            $table->enum('resiko', ['normal', 'tinggi'])->default('normal'); // isi konseling

            $table->timestamps();
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
