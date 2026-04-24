<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KonselingRule;

class KonselingRuleSeeder extends Seeder
{
    public function run(): void
    {
        // reset table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('konseling_rules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $rules = [

            /*
            |--------------------------------------------------------------------------
            | TTD RULE (10)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'A',
                'parameter' => 'kepatuhan_persen',
                'operator' => '<',
                'value' => 50,
                'isi_konseling' => 'Kepatuhan minum TTD sangat rendah',
                'priority' => 5,
                'score' => 3,
                'is_risk' => 1,
                'label' => 'ttd_rendah'
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'B',
                'parameter' => 'kepatuhan_persen',
                'operator' => '>=',
                'value' => 50,
                'isi_konseling' => 'Kepatuhan cukup, tingkatkan konsistensi',
                'priority' => 3,
                'score' => 2,
                'is_risk' => 0
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'C',
                'parameter' => 'kepatuhan_persen',
                'operator' => '>=',
                'value' => 80,
                'isi_konseling' => 'Kepatuhan minum TTD sangat baik',
                'priority' => 1,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'risiko_anemia',
                'logic_group' => 'A',
                'parameter' => 'total_tidak',
                'operator' => '>',
                'value' => 3,
                'isi_konseling' => 'Risiko anemia meningkat karena sering tidak minum TTD',
                'priority' => 5,
                'score' => 3,
                'is_risk' => 1
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'risiko_trimester',
                'logic_group' => 'A',
                'parameter' => 'usia_minggu',
                'operator' => '>=',
                'value' => 28,
                'isi_konseling' => 'Trimester akhir membutuhkan zat besi lebih tinggi',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'bulan6',
                'logic_group' => 'A',
                'parameter' => 'bulan_6',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'TTD bulan terakhir belum diminum',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'edukasi',
                'logic_group' => 'A',
                'parameter' => 'total_patuh',
                'operator' => '<',
                'value' => 3,
                'isi_konseling' => 'Minum TTD setiap hari membantu mencegah anemia',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'rujukan',
                'logic_group' => 'A',
                'parameter' => 'total_tidak',
                'operator' => '>=',
                'value' => 5,
                'isi_konseling' => 'Segera konsultasi ke bidan atau puskesmas',
                'priority' => 6,
                'score' => 4,
                'is_risk' => 1
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'kepatuhan',
                'logic_group' => 'D',
                'parameter' => 'kepatuhan_persen',
                'operator' => '<',
                'value' => 30,
                'isi_konseling' => 'Kepatuhan sangat rendah, perlu edukasi intensif',
                'priority' => 7,
                'score' => 4,
                'is_risk' => 1
            ],

            [
                'kategori' => 'ttd',
                'rule_group' => 'edukasi',
                'logic_group' => 'B',
                'parameter' => 'kepatuhan_persen',
                'operator' => '>=',
                'value' => 90,
                'isi_konseling' => 'Pertahankan kebiasaan minum TTD setiap hari',
                'priority' => 1,
                'score' => 1,
                'is_risk' => 0
            ],


            /*
            |--------------------------------------------------------------------------
            | PMBA RULE (12)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'pmba',
                'rule_group' => 'protein',
                'logic_group' => 'A',
                'parameter' => 'protein_hewani',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan protein hewani untuk pertumbuhan anak',
                'priority' => 3,
                'score' => 2,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'protein',
                'logic_group' => 'B',
                'parameter' => 'protein_nabati',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan sumber protein nabati seperti kacang',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'sayur',
                'logic_group' => 'A',
                'parameter' => 'sayur',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan sayur untuk vitamin dan mineral',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'buah',
                'logic_group' => 'A',
                'parameter' => 'buah',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan buah sebagai sumber vitamin',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'porsi',
                'logic_group' => 'A',
                'parameter' => 'porsi',
                'operator' => '=',
                'value' => 'kurang',
                'isi_konseling' => 'Porsi makan masih kurang',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'vitaminA',
                'logic_group' => 'A',
                'parameter' => 'vitamin_a',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan makanan kaya vitamin A',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'susu',
                'logic_group' => 'A',
                'parameter' => 'susu',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Susu dapat membantu memenuhi kebutuhan energi',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'telur',
                'logic_group' => 'A',
                'parameter' => 'telur',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Telur merupakan sumber protein berkualitas tinggi',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'asi',
                'logic_group' => 'A',
                'parameter' => 'asi',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'ASI tetap penting diberikan bersama MPASI',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'frekuensi',
                'logic_group' => 'A',
                'parameter' => 'frekuensi_makan',
                'operator' => '<',
                'value' => 3,
                'isi_konseling' => 'Frekuensi makan anak masih kurang',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'keragaman',
                'logic_group' => 'A',
                'parameter' => 'mdd_score',
                'operator' => '<',
                'value' => 4,
                'isi_konseling' => 'Keragaman makanan masih kurang',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'pmba',
                'rule_group' => 'mad',
                'logic_group' => 'A',
                'parameter' => 'mad_status',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Minimum Acceptable Diet belum tercapai',
                'priority' => 5,
                'score' => 3,
                'is_risk' => 1
            ],


            /*
            |--------------------------------------------------------------------------
            | MPASI RULE WHO (15)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'mpasi',
                'rule_group' => 'usia',
                'logic_group' => 'A',
                'parameter' => 'usia_bulan',
                'operator' => '<',
                'value' => 6,
                'isi_konseling' => 'MPASI sebaiknya dimulai usia 6 bulan',
                'priority' => 6,
                'score' => 3,
                'is_risk' => 1
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'usia',
                'logic_group' => 'B',
                'parameter' => 'usia_bulan',
                'operator' => '>=',
                'value' => 6,
                'isi_konseling' => 'MPASI sudah dapat diberikan',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mdd',
                'logic_group' => 'A',
                'parameter' => 'mdd_score',
                'operator' => '<',
                'value' => 5,
                'isi_konseling' => 'Keragaman makanan belum memenuhi standar WHO',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mmf',
                'logic_group' => 'A',
                'parameter' => 'frekuensi_makan',
                'operator' => '<',
                'value' => 2,
                'isi_konseling' => 'Frekuensi makan kurang dari standar',
                'priority' => 4,
                'score' => 2,
                'is_risk' => 1
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'protein',
                'logic_group' => 'A',
                'parameter' => 'protein_hewani',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan protein hewani',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'sayur',
                'logic_group' => 'A',
                'parameter' => 'sayur',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Sayur penting untuk vitamin',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'buah',
                'logic_group' => 'A',
                'parameter' => 'buah',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan buah untuk serat dan vitamin',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'mad',
                'logic_group' => 'A',
                'parameter' => 'mad_status',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'MAD belum tercapai',
                'priority' => 5,
                'score' => 3,
                'is_risk' => 1
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'edukasi',
                'logic_group' => 'A',
                'parameter' => 'porsi',
                'operator' => '=',
                'value' => 'kurang',
                'isi_konseling' => 'Porsi MPASI masih kurang',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'edukasi',
                'logic_group' => 'B',
                'parameter' => 'telur',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Telur dapat diberikan sebagai sumber protein',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'edukasi',
                'logic_group' => 'C',
                'parameter' => 'kacang',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan kacang sebagai sumber protein nabati',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'energi',
                'logic_group' => 'A',
                'parameter' => 'karbohidrat',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Karbohidrat penting sebagai sumber energi',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'lemak',
                'logic_group' => 'A',
                'parameter' => 'susu',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Tambahkan susu untuk energi tambahan',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'vitamin',
                'logic_group' => 'A',
                'parameter' => 'vitamin_a',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Vitamin A penting untuk kesehatan mata',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'mpasi',
                'rule_group' => 'asi',
                'logic_group' => 'A',
                'parameter' => 'asi',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'ASI tetap diberikan bersama MPASI',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],


            /*
            |--------------------------------------------------------------------------
            | ASI EKSKLUSIF RULE (8)
            |--------------------------------------------------------------------------
            */

            [
                'kategori' => 'asi',
                'rule_group' => 'asi_eks',
                'logic_group' => 'A',
                'parameter' => 'usia_bulan',
                'operator' => '<',
                'value' => 6,
                'isi_konseling' => 'ASI eksklusif dianjurkan sampai usia 6 bulan',
                'priority' => 5,
                'score' => 2,
                'is_risk' => 0
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'asi_eks',
                'logic_group' => 'B',
                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'ASI eksklusif belum tercapai',
                'priority' => 6,
                'score' => 3,
                'is_risk' => 1
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'lanjut',
                'logic_group' => 'A',
                'parameter' => 'usia_bulan',
                'operator' => '>=',
                'value' => 6,
                'isi_konseling' => 'Lanjutkan ASI dengan MPASI',
                'priority' => 3,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'edukasi',
                'logic_group' => 'A',
                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 1,
                'isi_konseling' => 'Pertahankan pemberian ASI',
                'priority' => 1,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'edukasi',
                'logic_group' => 'B',
                'parameter' => 'bulan_ke',
                'operator' => '>',
                'value' => 6,
                'isi_konseling' => 'ASI tetap diberikan sampai 2 tahun',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'risiko',
                'logic_group' => 'A',
                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Risiko kekurangan gizi meningkat tanpa ASI',
                'priority' => 6,
                'score' => 3,
                'is_risk' => 1
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'dukungan',
                'logic_group' => 'A',
                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 1,
                'isi_konseling' => 'Dukungan keluarga penting untuk keberhasilan ASI',
                'priority' => 2,
                'score' => 1,
                'is_risk' => 0
            ],

            [
                'kategori' => 'asi',
                'rule_group' => 'rujukan',
                'logic_group' => 'A',
                'parameter' => 'status_asi',
                'operator' => '=',
                'value' => 0,
                'isi_konseling' => 'Konsultasikan ke tenaga kesehatan mengenai pemberian ASI',
                'priority' => 7,
                'score' => 3,
                'is_risk' => 1
            ],
        ];

        foreach ($rules as $rule) {
            KonselingRule::create($rule);
        }
    }
}
