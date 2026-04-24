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
        Schema::create('pmbas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anaks')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('usia_bulan');
            $table->integer('frekuensi_makan')->nullable();
            $table->enum('tekstur', ['lumat', 'lembek', 'keluarga'])->nullable();
            $table->enum('porsi', ['kurang', 'cukup'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmbas');
    }
};
