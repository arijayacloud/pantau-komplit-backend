<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Konseling;

class KonselingSeeder extends Seeder
{
    public function run(): void
    {
        Konseling::insert([

            // =========================
            // 🤰 KEHAMILAN - TRIMESTER 1
            // =========================
            [
                'judul' => 'Trimester 1 - Nutrisi',
                'min_minggu' => 0,
                'max_minggu' => 12,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Konsumsi asam folat untuk mencegah cacat janin',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Trimester 1 - Istirahat',
                'min_minggu' => 0,
                'max_minggu' => 12,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Istirahat cukup dan hindari aktivitas berat',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =========================
            // 🤰 KEHAMILAN - TRIMESTER 2
            // =========================
            [
                'judul' => 'Trimester 2 - Gizi',
                'min_minggu' => 13,
                'max_minggu' => 27,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Perbanyak konsumsi protein, zat besi, dan kalsium',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Trimester 2 - Aktivitas',
                'min_minggu' => 13,
                'max_minggu' => 27,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Lakukan aktivitas ringan seperti jalan pagi',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =========================
            // 🤰 KEHAMILAN - TRIMESTER 3
            // =========================
            [
                'judul' => 'Trimester 3 - Persalinan',
                'min_minggu' => 28,
                'max_minggu' => 40,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Persiapkan persalinan dan kenali tanda bahaya',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Trimester 3 - Kontrol',
                'min_minggu' => 28,
                'max_minggu' => 40,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Periksa kehamilan minimal 2 minggu sekali',
                'kategori' => 'kehamilan',
                'resiko' => 'normal',
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =========================
            // 🚨 RISIKO TINGGI (GLOBAL)
            // =========================
            [
                'judul' => 'Risiko Tinggi - Umum',
                'min_minggu' => null,
                'max_minggu' => null,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Segera rujuk ke fasilitas kesehatan',
                'kategori' => 'kehamilan',
                'resiko' => 'tinggi',
                'priority' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Risiko - Anemia',
                'min_minggu' => null,
                'max_minggu' => null,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Tingkatkan konsumsi TTD dan makanan tinggi zat besi',
                'kategori' => 'kehamilan',
                'resiko' => 'tinggi',
                'priority' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Risiko - Kepatuhan Rendah',
                'min_minggu' => null,
                'max_minggu' => null,
                'min_bulan' => null,
                'max_bulan' => null,
                'materi' => 'Edukasi pentingnya konsumsi TTD setiap hari',
                'kategori' => 'kehamilan',
                'resiko' => 'tinggi',
                'priority' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
