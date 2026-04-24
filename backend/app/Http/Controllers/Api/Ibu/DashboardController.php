<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use App\Models\Anak;
use App\Models\Kehamilan;
use App\Models\Pmba;
use App\Models\AsiEksklusif;
use App\Models\Ibu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔥 ambil data ibu dulu
        $ibu = Ibu::where('user_id', $user->id)->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        // ======================
        // DATA MILIK IBU
        // ======================
        $totalKehamilan = Kehamilan::where('ibu_id', $ibu->id)->count();

        $totalAnak = Anak::where('ibu_id', $ibu->id)->count();

        $totalAsi = AsiEksklusif::whereHas('anak', function ($q) use ($ibu) {
            $q->where('ibu_id', $ibu->id);
        })->count();

        $totalPmba = Pmba::whereHas('anak', function ($q) use ($ibu) {
            $q->where('ibu_id', $ibu->id);
        })->count();

        // ======================
        // KEHAMILAN AKTIF
        // ======================
        $kehamilanAktif = Kehamilan::where('ibu_id', $ibu->id)
            ->where('status', 'hamil')
            ->latest()
            ->first();

        $usiaKehamilan = null;
        $perkiraanLahir = null;

        if ($kehamilanAktif && $kehamilanAktif->hpht) {
            $hpht = Carbon::parse($kehamilanAktif->hpht);

            $usiaKehamilan = $hpht->diffInWeeks(now());
            $perkiraanLahir = $hpht->copy()->addDays(280)->format('Y-m-d');
        }

        // ======================
        // STATUS ASI
        // ======================
        $asiBaik = AsiEksklusif::whereHas('anak', function ($q) use ($ibu) {
            $q->where('ibu_id', $ibu->id);
        })->where('status_asi', true)->count();

        $asiKurang = AsiEksklusif::whereHas('anak', function ($q) use ($ibu) {
            $q->where('ibu_id', $ibu->id);
        })->where('status_asi', false)->count();

        // ======================
        // PMBA WARNING
        // ======================
        $pmbaKurang = Pmba::whereHas('anak', function ($q) use ($ibu) {
            $q->where('ibu_id', $ibu->id);
        })->where('porsi', 'kurang')->count();

        // ======================
        // DATA TERBARU
        // ======================
        $anakTerbaru = Anak::where('ibu_id', $ibu->id)
            ->latest()
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard ibu berhasil',

            'total_kehamilan' => (int) $totalKehamilan,
            'total_anak' => (int) $totalAnak,
            'total_asi' => (int) $totalAsi,
            'total_pmba' => (int) $totalPmba,

            'kehamilan' => [
                'usia_minggu' => $usiaKehamilan,
                'perkiraan_lahir' => $perkiraanLahir,
            ],

            'asi' => [
                'baik' => (int) $asiBaik,
                'kurang' => (int) $asiKurang,
            ],

            'pmba' => [
                'kurang' => (int) $pmbaKurang,
            ],

            'recent' => [
                'anak' => $anakTerbaru,
            ]
        ]);
    }
}
