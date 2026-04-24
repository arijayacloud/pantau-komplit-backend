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
        Schema::table('remaja_putris', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('nama');
            $table->string('no_hp')->nullable()->after('tanggal_lahir');

            $table->integer('hb')->nullable()->after('alamat');
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();

            $table->boolean('sudah_menstruasi')->default(false);
            $table->date('tanggal_menstruasi_terakhir')->nullable();

            $table->boolean('konsumsi_ttd')->default(false);
            $table->integer('jumlah_ttd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remaja_putris', function (Blueprint $table) {
            //
        });
    }
};
