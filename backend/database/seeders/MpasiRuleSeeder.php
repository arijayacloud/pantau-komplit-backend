<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonselingRule;

class MpasiRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [

            /*
            |--------------------------------------------------------------------------
            | USIA (VALIDASI)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'usia',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'usia_bulan',
                'operator' => '<',
                'value' => 6,
                'data_type' => 'number',

                'isi_konseling' => 'MPASI sebaiknya dimulai usia 6 bulan',
                'output_type' => 'warning',

                'priority' => 10,
                'score' => 5,
                'is_risk' => 1,
                'label' => 'mpasi_dini'
            ],

            /*
            |--------------------------------------------------------------------------
            | MDD (KERAGAMAN MAKANAN)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mdd',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'mdd_score',
                'operator' => '<',
                'value' => 4,
                'data_type' => 'number',

                'isi_konseling' => 'Keragaman makanan sangat kurang',
                'output_type' => 'warning',

                'priority' => 8,
                'score' => 4,
                'is_risk' => 1,
                'label' => 'mdd_sangat_kurang'
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mdd',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'mdd_score',
                'operator' => '<',
                'value' => 5,
                'data_type' => 'number',

                'isi_konseling' => 'Keragaman makanan belum optimal',
                'output_type' => 'konseling',

                'priority' => 5,
                'score' => 2,
                'is_risk' => 0,
                'label' => 'mdd_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | FREKUENSI MAKAN (MMF)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'frekuensi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'frekuensi_makan',
                'operator' => '<',
                'value' => 2,
                'data_type' => 'number',

                'isi_konseling' => 'Frekuensi makan sangat kurang',
                'output_type' => 'warning',

                'priority' => 8,
                'score' => 4,
                'is_risk' => 1,
                'label' => 'mmf_sangat_kurang'
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'frekuensi',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'frekuensi_makan',
                'operator' => '<',
                'value' => 3,
                'data_type' => 'number',

                'isi_konseling' => 'Frekuensi makan kurang',
                'output_type' => 'warning',

                'priority' => 6,
                'score' => 3,
                'is_risk' => 1,
                'label' => 'mmf_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | MAD (GABUNGAN)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mad',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'mad_status',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Minimum Acceptable Diet belum tercapai',
                'output_type' => 'warning',

                'priority' => 9,
                'score' => 5,
                'is_risk' => 1,
                'label' => 'mad_gagal'
            ],

            /*
            |--------------------------------------------------------------------------
            | PROTEIN
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'protein',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'protein_hewani',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Tambahkan protein hewani (telur, ikan, daging)',
                'output_type' => 'konseling',

                'priority' => 5,
                'score' => 2,
                'is_risk' => 0,
                'label' => 'protein_hewani_kurang'
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'protein',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'protein_nabati',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Tambahkan protein nabati (kacang-kacangan)',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'protein_nabati_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | VITAMIN & MINERAL
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'vitamin',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'sayur',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Tambahkan sayur untuk vitamin dan mineral',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'sayur_kurang'
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'vitamin',
                'logic_group' => 'B',
                'logic_operator' => 'AND',

                'parameter' => 'buah',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Tambahkan buah untuk serat dan vitamin',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'buah_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | ENERGI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'energi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'karbohidrat',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'Karbohidrat penting sebagai sumber energi',
                'output_type' => 'konseling',

                'priority' => 3,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'karbo_kurang'
            ],

            /*
            |--------------------------------------------------------------------------
            | ASI
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'asi',
                'logic_group' => 'A',
                'logic_operator' => 'AND',

                'parameter' => 'asi',
                'operator' => '=',
                'value' => 0,
                'data_type' => 'number',

                'isi_konseling' => 'ASI tetap penting diberikan bersama MPASI',
                'output_type' => 'konseling',

                'priority' => 4,
                'score' => 1,
                'is_risk' => 0,
                'label' => 'asi_tidak'
            ],

        ];

        KonselingRule::insert($rules);
    }
}
