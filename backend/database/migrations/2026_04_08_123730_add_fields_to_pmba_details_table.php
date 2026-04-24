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
        Schema::table('pmba_details', function (Blueprint $table) {
            $table->integer('mdd_score')->nullable();
            $table->boolean('mmf_status')->nullable();
            $table->boolean('mad_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pmba_details', function (Blueprint $table) {
            //
        });
    }
};
