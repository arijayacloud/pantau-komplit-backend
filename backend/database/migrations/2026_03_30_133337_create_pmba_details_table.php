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
        Schema::create('pmba_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pmba_id')->constrained('pmbas')->cascadeOnDelete();
            $table->boolean('karbohidrat')->default(false);
            $table->boolean('protein_hewani')->default(false);
            $table->boolean('protein_nabati')->default(false);
            $table->boolean('sayur')->default(false);
            $table->boolean('buah')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pmba_details');
    }
};
