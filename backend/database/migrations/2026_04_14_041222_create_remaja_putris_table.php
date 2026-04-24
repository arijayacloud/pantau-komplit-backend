<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remaja_putris', function (Blueprint $table) {
            $table->id();

            // IDENTITAS
            $table->string('nama');
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();

            // SEKOLAH
            $table->string('sekolah')->nullable();
            $table->string('kelas')->nullable();

            // ALAMAT
            $table->text('alamat')->nullable();

            // KESEHATAN
            $table->integer('hb')->nullable(); // hemoglobin
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();

            // MENSTRUASI
            $table->boolean('sudah_menstruasi')->default(false);
            $table->date('tanggal_menstruasi_terakhir')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remaja_putris');
    }
};
