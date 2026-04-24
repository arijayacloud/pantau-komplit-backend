<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konseling_rules', function (Blueprint $table) {

            // 🔥 tambah kolom baru
            $table->string('logic_group')->nullable()->after('rule_group');

            $table->enum('data_type', ['number', 'boolean', 'string'])
                ->default('number')
                ->after('value');

            $table->enum('output_type', ['konseling', 'warning', 'score'])
                ->default('konseling')
                ->after('isi_konseling');

            $table->string('label')->nullable()->after('is_risk');

            // 🔥 ubah kolom value dari double → string
            $table->string('value')->change();

            $table->index('kategori');
            $table->index('parameter');
            $table->index('logic_group');
        });
    }

    public function down(): void
    {
        Schema::table('konseling_rules', function (Blueprint $table) {

            // rollback kolom baru
            $table->dropColumn([
                'logic_group',
                'data_type',
                'output_type',
                'label'
            ]);

            // balikin value ke double
            $table->double('value')->change();
        });
    }
};
