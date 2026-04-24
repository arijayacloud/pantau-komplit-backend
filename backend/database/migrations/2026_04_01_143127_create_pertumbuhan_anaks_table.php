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
        Schema::create('pertumbuhan_anaks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anak_id')
                ->constrained('anaks')
                ->cascadeOnDelete();

            $table->date('tanggal'); // tanggal pengukuran

            $table->float('berat_badan')->nullable(); // kg
            $table->float('tinggi_badan')->nullable(); // cm
            $table->float('lingkar_kepala')->nullable(); // opsional
            $table->float('z_score_bb')->nullable();
            $table->float('z_score_tb')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertumbuhan_anaks');
    }
};
