<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use App\Models\Monitoring;
use App\Models\Kehamilan;
use App\Models\Ibu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Konseling;
use Carbon\Carbon;
use App\Services\MonitoringService;
use App\Services\DecisionRuleEngine;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    private $ruleEngine;

    public function __construct(DecisionRuleEngine $ruleEngine)
    {
        $this->ruleEngine = $ruleEngine;
    }

    /**
     * 📋 LIST MONITORING (HANYA MILIK IBU LOGIN)
     */
    public function index()
    {
        $userId = Auth::id();

        $ibu = Ibu::where('user_id', $userId)->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $data = Monitoring::with('kehamilan')
            ->whereHas('kehamilan', function ($q) use ($ibu) {
                $q->where('ibu_id', $ibu->id); // ✅ FIX
            })
            ->latest()
            ->paginate(10);

        // optional: format biar konsisten
        $data->getCollection()->transform(fn($item) => $this->format($item));

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * 🔍 DETAIL
     */
    public function show($id)
    {
        $userId = Auth::id();

        $ibu = Ibu::where('user_id', $userId)->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $monitoring = Monitoring::where('id', $id)
            ->whereHas('kehamilan', function ($q) use ($ibu) {
                $q->where('ibu_id', $ibu->id); // ✅ FIX
            })
            ->with('kehamilan')
            ->first();

        if (!$monitoring) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->format($monitoring)
        ]);
    }

    /**
     * ❌ IBU TIDAK BOLEH CREATE (OPSIONAL)
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Tidak diizinkan'
        ], 403);
    }

    /**
     * ❌ IBU TIDAK BOLEH UPDATE
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Tidak diizinkan'
        ], 403);
    }

    /**
     * ❌ IBU TIDAK BOLEH DELETE
     */
    public function destroy($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Tidak diizinkan'
        ], 403);
    }

    /**
     * 🎯 FORMAT
     */
    private function format($m)
    {
        $bulan = [
            (int) ($m->bulan_1 ?? 0),
            (int) ($m->bulan_2 ?? 0),
            (int) ($m->bulan_3 ?? 0),
            (int) ($m->bulan_4 ?? 0),
            (int) ($m->bulan_5 ?? 0),
            (int) ($m->bulan_6 ?? 0),
        ];

        $hitung = MonitoringService::hitungKepatuhan($bulan);

        $statusRisk = 'aman';
        if ($m->is_risk) {
            $statusRisk = $hitung['persen'] < 50 ? 'tinggi' : 'sedang';
        }

        return [
            'id' => $m->id,
            'tanggal' => $m->tanggal,
            'bulan' => $bulan,
            'kepatuhan_persen' => $hitung['persen'],
            'is_risk' => (bool) $m->is_risk,
            'status_risk' => $statusRisk,

            'kehamilan' => [
                'hpht' => $m->kehamilan?->hpht,
            ],

            'konseling' => $m->hasil_konseling ?? [],
        ];
    }

    public function preview(Request $request)
    {
        $request->validate([
            'kehamilan_id' => 'required|exists:kehamilans,id',
        ]);

        $userId = Auth::id();

        $ibu = Ibu::where('user_id', $userId)->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $kehamilan = Kehamilan::where('id', $request->kehamilan_id)
            ->where('ibu_id', $ibu->id) // ✅ SECURITY FIX
            ->first();

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $kehamilan = Kehamilan::findOrFail($request->kehamilan_id);

        // 🔥 ambil input bulan
        $bulan = [
            (int) ($request->bulan_1 ?? 0),
            (int) ($request->bulan_2 ?? 0),
            (int) ($request->bulan_3 ?? 0),
            (int) ($request->bulan_4 ?? 0),
            (int) ($request->bulan_5 ?? 0),
            (int) ($request->bulan_6 ?? 0),
        ];

        // 🔥 pakai central logic
        $hasil = $this->processMonitoring($kehamilan, $bulan);

        return response()->json([
            'success' => true,
            'usia_minggu' => $hasil['usia_minggu'],
            'total_patuh' => $hasil['total_patuh'],
            'total_tidak' => $hasil['total_tidak'],
            'kepatuhan_persen' => $hasil['persen'],
            'is_risk' => $hasil['is_risk'],
            'status_risk' => $hasil['status_risk'], // 🔥 tambahan
            'konseling' => $hasil['materi']
        ]);
    }

    private function processMonitoring($kehamilan, $bulan)
    {
        // =========================
        // 🔥 HITUNG KEPATUHAN
        // =========================
        $hitung = MonitoringService::hitungKepatuhan($bulan);

        $totalPatuh = $hitung['total_patuh'];
        $totalTidak = $hitung['total_tidak'];
        $persen = $hitung['persen'];

        // =========================
        // 🔥 HITUNG USIA KEHAMILAN
        // =========================
        $usiaMinggu = 0;

        if (!empty($kehamilan->hpht)) {
            $usiaMinggu = Carbon::parse($kehamilan->hpht)
                ->diffInWeeks(now(), false);

            if ($usiaMinggu < 0) $usiaMinggu = 0;
        }

        // =========================
        // 🔥 RULE ENGINE
        // =========================
        $ruleData = [
            'bulan_1' => $bulan[0],
            'bulan_2' => $bulan[1],
            'bulan_3' => $bulan[2],
            'bulan_4' => $bulan[3],
            'bulan_5' => $bulan[4],
            'bulan_6' => $bulan[5],
            'total_patuh' => $totalPatuh,
            'total_tidak' => $totalTidak,
            'kepatuhan_persen' => $persen,
            'usia_minggu' => $usiaMinggu,
            'bulan_aktif' => $totalPatuh,
        ];

        $result = $this->ruleEngine->run('ttd', $ruleData);

        // =========================
        // 🔥 HITUNG SCORE RULE
        // =========================
        $score = collect($result['hasil'])->sum('skor');

        // =========================
        // 🔥 MANUAL RISK (LEBIH MASUK AKAL)
        // =========================
        $isRiskManual = false;

        if ($persen < 40) {
            $isRiskManual = true;
        }

        if ($usiaMinggu >= 28 && $persen < 60) {
            $isRiskManual = true;
        }

        // =========================
        // 🔥 STATUS RISK (REVISED)
        // =========================
        $statusRisk = 'aman';

        // 🔥 rule based
        if ($score >= 10) {
            $statusRisk = 'tinggi';
        } elseif ($score >= 6) {
            $statusRisk = 'sedang';
        }

        // 🔥 override manual (lebih prioritas)
        if ($persen < 40) {
            $statusRisk = 'tinggi';
        }

        if ($usiaMinggu >= 28 && $persen < 60) {
            $statusRisk = 'tinggi';
        }

        $isRiskFinal = in_array($statusRisk, ['tinggi']);

        // =========================
        // 🔥 KONSELING DARI RULE
        // =========================
        $materiRule = collect($result['hasil'])
            ->groupBy('group')
            ->map(fn($items) => collect($items)->first())
            ->pluck('isi')
            ->filter()
            ->values()
            ->toArray();

        // =========================
        // 🔥 KONSELING BERDASARKAN USIA
        // =========================
        $materiUmur = Konseling::where('kategori', 'kehamilan')
            ->where(function ($q) use ($usiaMinggu) {
                $q->whereNull('min_minggu')
                    ->orWhere('min_minggu', '<=', $usiaMinggu);
            })
            ->where(function ($q) use ($usiaMinggu) {
                $q->whereNull('max_minggu')
                    ->orWhere('max_minggu', '>=', $usiaMinggu);
            })
            ->pluck('materi')
            ->toArray();

        // =========================
        // 🔥 KONSELING TAMBAHAN JIKA RISIKO TINGGI
        // =========================
        $materiRisiko = $isRiskFinal
            ? Konseling::where('resiko', 'tinggi')->pluck('materi')->toArray()
            : [];

        // =========================
        // 🔥 FINAL MERGE (NO DUPLICATE)
        // =========================
        $materiFinal = array_values(array_unique(array_merge(
            $materiRule,
            $materiUmur,
            $materiRisiko
        )));

        return [
            'total_patuh' => $totalPatuh,
            'total_tidak' => $totalTidak,
            'persen' => $persen,
            'usia_minggu' => $usiaMinggu,
            'is_risk' => $isRiskFinal,
            'status_risk' => $statusRisk, // 🔥 TAMBAHAN
            'materi' => $materiFinal
        ];
    }
}
