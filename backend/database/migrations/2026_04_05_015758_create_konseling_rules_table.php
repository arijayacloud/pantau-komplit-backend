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
        Schema::create('konseling_rules', function (Blueprint $table) {
            $table->id();

            $table->enum('kategori', ['ttd', 'bumil', 'anak', 'asi']);

            $table->string('rule_group'); // 🔥 BARU
            $table->string('parameter');

            $table->string('operator');
            $table->double('value'); // 🔥 FIX

            $table->text('isi_konseling');

            $table->integer('priority')->default(1);
            $table->integer('score')->default(0);

            $table->boolean('is_risk')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konseling_rules');
    }
};
