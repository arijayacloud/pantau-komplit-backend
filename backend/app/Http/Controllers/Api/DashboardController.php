<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ibu;
use App\Models\Kehamilan;
use App\Models\Anak;
use App\Models\Pmba;
use App\Models\RemajaPutri;
use App\Models\TtdIbu;
use App\Models\AsiEksklusif;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ======================
        // SUMMARY (FLAT - BIAR MUDAH DI FLUTTER)
        // ======================
        $totalIbu = Ibu::count();

        $totalKehamilan = Kehamilan::where('status', 'hamil')->count();

        $totalAnak = Anak::count();
        $totalRemaja = RemajaPutri::count();
        $totalPmba = Pmba::count();
        $totalAsi = AsiEksklusif::count();

        // ======================
        // GRAFIK BULANAN
        // ======================
        $grafik = [];

        for ($i = 1; $i <= 12; $i++) {
            $grafik[] = [
                'bulan' => $i,
                'ibu' => Ibu::whereMonth('created_at', $i)->count(),
                'anak' => Anak::whereMonth('created_at', $i)->count(),
                'kehamilan' => Kehamilan::whereMonth('created_at', $i)->count(),
            ];
        }

        // ======================
        // KEHAMILAN TRIMESTER
        // ======================
        $trimester1 = Kehamilan::whereNotNull('hpht')
            ->get()
            ->filter(function ($item) {
                return \Carbon\Carbon::parse($item->hpht)->diffInWeeks(now()) <= 12;
            })->count();

        $trimester2 = Kehamilan::whereNotNull('hpht')
            ->get()
            ->filter(function ($item) {
                $usia = \Carbon\Carbon::parse($item->hpht)->diffInWeeks(now());
                return $usia >= 13 && $usia <= 27;
            })->count();

        $trimester3 = Kehamilan::whereNotNull('hpht')
            ->get()
            ->filter(function ($item) {
                return \Carbon\Carbon::parse($item->hpht)->diffInWeeks(now()) >= 28;
            })->count();

        // ======================
        // STATUS ASI
        // ======================
        $asiBaik = AsiEksklusif::where('status_asi', true)->count();
        $asiKurang = AsiEksklusif::where('status_asi', false)->count();

        // ======================
        // STATUS TTD
        // ======================
        $ttdBaik = TtdIbu::where('jumlah_diminum', '>=', 30)->count();
        $ttdKurang = TtdIbu::where('jumlah_diminum', '<', 30)->count();

        // ======================
        // PMBA WARNING
        // ======================
        $pmbaKurang = Pmba::where('porsi', 'kurang')->count();

        // ======================
        // DATA TERBARU
        // ======================
        $anakTerbaru = Anak::latest()->take(5)->get();
        $ibuTerbaru = Ibu::latest()->take(5)->get();

        // ======================
        // RESPONSE FINAL
        // ======================
        return response()->json([
            'success' => true,
            'message' => 'Dashboard berhasil',

            // 🔥 FLAT (langsung dipakai Flutter)
            'total_ibu' => $totalIbu,
            'total_kehamilan' => $totalKehamilan,
            'total_anak' => $totalAnak,
            'total_remaja' => $totalRemaja,
            'total_pmba' => $totalPmba,
            'total_asi' => $totalAsi,

            // 📊 grafik
            'grafik_bulanan' => $grafik,

            // 🤰 kehamilan
            'kehamilan' => [
                'trimester_1' => $trimester1,
                'trimester_2' => $trimester2,
                'trimester_3' => $trimester3,
            ],

            // 🍼 asi
            'asi' => [
                'baik' => $asiBaik,
                'kurang' => $asiKurang,
            ],

            // 💊 ttd
            'ttd' => [
                'baik' => $ttdBaik,
                'kurang' => $ttdKurang,
            ],

            // 🍽️ pmba
            'pmba' => [
                'kurang' => $pmbaKurang,
            ],

            // 📌 recent
            'recent' => [
                'anak' => $anakTerbaru,
                'ibu' => $ibuTerbaru,
            ]
        ]);
    }
}
