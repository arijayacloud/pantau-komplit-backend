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
        Schema::create('ttd_ibus', function (Blueprint $table) {
            $table->id();

            // 🔥 relasi ke ibu (boleh tetap)
            $table->foreignId('ibu_id')
                ->constrained('ibus')
                ->cascadeOnDelete();

            // 🔥 OPTIONAL: relasi ke kehamilan (LEBIH TEPAT)
            $table->foreignId('kehamilan_id')
                ->nullable()
                ->constrained('kehamilans')
                ->cascadeOnDelete();

            $table->date('tanggal_dapat')->nullable();

            // bulan kehamilan
            $table->integer('bulan_ke')->nullable();

            // jumlah tablet diminum
            $table->integer('jumlah_diminum')->default(0);

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ttd_ibus');
    }
};
