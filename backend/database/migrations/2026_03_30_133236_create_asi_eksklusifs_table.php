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
        Schema::create('asi_eksklusifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anaks')->cascadeOnDelete();
            $table->integer('bulan_ke');
            $table->boolean('status_asi')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asi_eksklusifs');
    }
};
