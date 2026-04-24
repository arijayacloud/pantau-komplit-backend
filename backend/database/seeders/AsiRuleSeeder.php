<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonselingRule;

class AsiRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [

            /*
            |--------------------------------------------------------------------------
            | ASI EKSKLUSIF (0–6 BULAN)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'asi_eks',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'usia_bulan',
                'operator' => '<',
                'value' => 6,
                'data_type' => 'number',

                'isi_konseling' => 'ASI eksklusif dianjurkan sampai usia 6 bulan',
                'output_type' => 'konseling',

                'priority' => 5,
                'score' => 2,
                'is_risk' => 0,
                'label' => 'asi_eks_edukasi'
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'asi_eks',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'ASI eksklusif belum tercapai',
                'output_type' => 'warning',

                'priority' => 7,
                'score' => 4,
                'is_risk' => 1,
                'label' => 'asi_eks_gagal'
            ],

            /*
            |--------------------------------------------------------------------------
            | TRANSISI MPASI (>= 6 BULAN)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'transisi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'usia_bulan',
                'operator' => '>=',
                'value' => 6,
                'data_type' => 'number',

                'isi_konseling' => 'Mulai MPASI dan lanjutkan ASI',
                'output_type' => 'konseling',

                'priority' => 3,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'mpasi_mulai'
            ],

            /*
            |--------------------------------------------------------------------------
            | LANJUTAN ASI (6–24 BULAN)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'lanjutan',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'usia_bulan',
                'operator' => '<=',
                'value' => 24,
                'data_type' => 'number',

                'isi_konseling' => 'ASI tetap diberikan hingga usia 2 tahun',
                'output_type' => 'konseling',

                'priority' => 2,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'asi_lanjut'
            ],

            /*
            |--------------------------------------------------------------------------
            | FREKUENSI MENYUSUI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'frekuensi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'frekuensi_asi',
                'operator' => '<',
                'value' => 8,
                'data_type' => 'number',

                'isi_konseling' => 'Frekuensi menyusui kurang, tingkatkan pemberian ASI',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 2,
                'is_risk' => 0,
                'label' => 'asi_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | RISIKO TANPA ASI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'risiko',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Risiko kekurangan gizi meningkat tanpa ASI',
                'output_type' => 'warning',

                'priority' => 8,
                'score' => 5,
                'is_risk' => 1,
                'label' => 'risiko_tanpa_asi'
            ],

            /*
            |--------------------------------------------------------------------------
            | EDUKASI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'edukasi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 1,
                'data_type' => 'number',

                'isi_konseling' => 'Pertahankan pemberian ASI secara rutin',
                'output_type' => 'konseling',

                'priority' => 1,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'asi_baik'
            ],

            /*
            |--------------------------------------------------------------------------
            | DUKUNGAN
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'dukungan',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'dukungan_keluarga',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Dukungan keluarga penting untuk keberhasilan ASI',
                'output_type' => 'konseling',

                'priority' => 3,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'dukungan_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | RUJUKAN (CRITICAL)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'rujukan',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Segera konsultasi ke tenaga kesehatan terkait pemberian ASI',
                'output_type' => 'action',

                'priority' => 10,
                'score' => 6,
                'is_risk' => 1,
                'label' => 'rujukan_asi'
            ],
        ];

        KonselingRule::insert($rules);
    }
}
