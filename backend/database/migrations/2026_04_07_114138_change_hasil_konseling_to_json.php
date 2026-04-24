<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->json('hasil_konseling')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('monitorings', function (Blueprint $table) {
            $table->longText('hasil_konseling')->nullable()->change();
        });
    }
};
