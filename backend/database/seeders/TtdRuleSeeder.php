<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonselingRule;

class TtdRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [

            /*
            |--------------------------------------------------------------------------
            | KEPATUHAN
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'kepatuhan_persen',
                'operator' => '<',
                'value' => 30,
                'data_type' => 'number',

                'isi_konseling' => 'Kepatuhan sangat rendah, perlu edukasi intensif',
                'output_type' => 'warning',

                'priority' => 7,
                'score' => 5,
                'is_risk' => 1,
                'label' => 'ttd_sangat_rendah'
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'kepatuhan_persen',
                'operator' => '<',
                'value' => 50,
                'data_type' => 'number',

                'isi_konseling' => 'Kepatuhan rendah, tingkatkan konsumsi harian',
                'output_type' => 'warning',

                'priority' => 6,
                'score' => 4,
                'is_risk' => 1,
                'label' => 'ttd_rendah'
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'C',
                'logic_operator' => 'AND',

                'parameter' => 'kepatuhan_persen',
                'operator' => '<',
                'value' => 80,
                'data_type' => 'number',

                'isi_konseling' => 'Kepatuhan cukup, perlu ditingkatkan',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 2,
                'is_risk' => 0,
                'label' => 'ttd_cukup'
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'D',
                'logic_operator' => 'AND',

                'parameter' => 'kepatuhan_persen',
                'operator' => '>=',
                'value' => 80,
                'data_type' => 'number',

                'isi_konseling' => 'Kepatuhan sangat baik, pertahankan',
                'output_type' => 'konseling',

                'priority' => 2,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'ttd_baik'
            ],

            /*
            |--------------------------------------------------------------------------
            | RISIKO ANEMIA
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'anemia',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'total_tidak',
                'operator' => '>=',
                'value' => 5,
                'data_type' => 'number',

                'isi_konseling' => 'Risiko anemia tinggi, segera konsultasi',
                'output_type' => 'warning',

                'priority' => 8,
                'score' => 5,
                'is_risk' => 1,
                'label' => 'anemia_tinggi'
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'anemia',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'total_tidak',
                'operator' => '>',
                'value' => 3,
                'data_type' => 'number',

                'isi_konseling' => 'Risiko anemia meningkat',
                'output_type' => 'warning',

                'priority' => 6,
                'score' => 3,
                'is_risk' => 1,
                'label' => 'anemia_sedang'
            ],

            /*
            |--------------------------------------------------------------------------
            | TRIMESTER
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'trimester',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'usia_minggu',
                'operator' => '>=',
                'value' => 28,
                'data_type' => 'number',

                'isi_konseling' => 'Trimester akhir membutuhkan zat besi lebih tinggi',
                'output_type' => 'konseling',

                'priority' => 3,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'trimester_akhir'
            ],

            /*
            |--------------------------------------------------------------------------
            | BULAN TERAKHIR
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'bulan_terakhir',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'bulan_6',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'TTD bulan terakhir belum diminum',
                'output_type' => 'warning',

                'priority' => 5,
                'score' => 2,
                'is_risk' => 1,
                'label' => 'ttd_bulan_terakhir'
            ],

            /*
            |--------------------------------------------------------------------------
            | EDUKASI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'edukasi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'total_patuh',
                'operator' => '<',
                'value' => 3,
                'data_type' => 'number',

                'isi_konseling' => 'Minum TTD setiap hari membantu mencegah anemia',
                'output_type' => 'konseling',

                'priority' => 3,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'edukasi_ttd'
            ],

            /*
            |--------------------------------------------------------------------------
            | RUJUKAN (CRITICAL)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'rujukan',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'total_tidak',
                'operator' => '>=',
                'value' => 7,
                'data_type' => 'number',

                'isi_konseling' => 'Segera rujuk ke tenaga kesehatan',
                'output_type' => 'action',

                'priority' => 10,
                'score' => 6,
                'is_risk' => 1,
                'label' => 'rujukan_urgent'
            ],
        ];

        KonselingRule::insert($rules);
    }
}
