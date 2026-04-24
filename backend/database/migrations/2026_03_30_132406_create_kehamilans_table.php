<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kehamilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_id')->constrained('ibus')->cascadeOnDelete();
            $table->date('hpht')->nullable();
            $table->enum('status', ['hamil', 'selesai', 'gugur'])->default('hamil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehamilans');
    }
};
