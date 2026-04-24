<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Monitoring;
use App\Models\Kehamilan;
use App\Models\Konseling;
use App\Models\KonselingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\MonitoringService;
use App\Services\DecisionRuleEngine;

class MonitoringController extends Controller
{

    private $ruleEngine;

    public function __construct(DecisionRuleEngine $ruleEngine)
    {
        $this->ruleEngine = $ruleEngine;
    }
    /**
     * 📋 LIST DATA MONITORING
     */
    public function index()
    {
        $data = Monitoring::with('kehamilan.ibu')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * 💾 SIMPAN DATA MONITORING
     */
    public function store(Request $request)
    {
        // =========================
        // 🔥 VALIDASI DASAR
        // =========================
        $request->validate([
            'kehamilan_id' => 'required|exists:kehamilans,id',
            'tanggal' => 'required|date',
        ]);

        $kehamilan = Kehamilan::findOrFail($request->kehamilan_id);

        // =========================
        // 🔥 AMBIL INPUT BULAN
        // =========================
        $bulan = [
            (int) ($request->bulan_1 ?? 0),
            (int) ($request->bulan_2 ?? 0),
            (int) ($request->bulan_3 ?? 0),
            (int) ($request->bulan_4 ?? 0),
            (int) ($request->bulan_5 ?? 0),
            (int) ($request->bulan_6 ?? 0),
        ];

        // =========================
        // 🔥 VALIDASI MINIMAL PILIH 1 BULAN
        // =========================
        if (!in_array(1, $bulan)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal pilih 1 bulan'
            ], 422);
        }

        // =========================
        // 🔥 VALIDASI BERURUTAN (ANTI LONCAT)
        // =========================
        for ($i = 1; $i < 6; $i++) {
            if ($bulan[$i] == 1 && $bulan[$i - 1] == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Isi bulan sebelumnya terlebih dahulu"
                ], 422);
            }
        }

        // =========================
        // 🔥 AMBIL DATA TERAKHIR
        // =========================
        $last = Monitoring::where('kehamilan_id', $request->kehamilan_id)
            ->latest()
            ->first();

        // =========================
        // 🔥 WAJIB MULAI DARI BULAN 1 (JIKA DATA PERTAMA)
        // =========================
        $firstActiveIndex = array_search(1, $bulan);

        if (!$last && $firstActiveIndex !== false && $firstActiveIndex !== 0) {
            return response()->json([
                'success' => false,
                'message' => "Monitoring pertama harus dimulai dari bulan 1"
            ], 422);
        }

        // =========================
        // 🔥 VALIDASI LANJUTAN (JIKA SUDAH ADA DATA)
        // =========================
        if ($last) {
            $bulanLast = [
                (int) $last->bulan_1,
                (int) $last->bulan_2,
                (int) $last->bulan_3,
                (int) $last->bulan_4,
                (int) $last->bulan_5,
                (int) $last->bulan_6,
            ];

            for ($i = 0; $i < 6; $i++) {

                // ❌ tidak boleh isi ulang bulan lama
                if ($bulan[$i] == 1 && $bulanLast[$i] == 1) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bulan ke-" . ($i + 1) . " sudah pernah diisi"
                    ], 422);
                }

                // ❌ tidak boleh loncat dari histori
                if ($bulan[$i] == 1 && $i > 0 && $bulanLast[$i - 1] == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Harus melanjutkan dari bulan sebelumnya"
                    ], 422);
                }
            }
        }

        // =========================
        // 🔥 PROCESS LOGIC
        // =========================
        $hasil = $this->processMonitoring($kehamilan, $bulan);

        // =========================
        // 🔥 SIMPAN DATA
        // =========================
        $monitoring = Monitoring::create([
            'kehamilan_id' => $request->kehamilan_id,
            'tanggal' => $request->tanggal,

            'bulan_1' => $bulan[0],
            'bulan_2' => $bulan[1],
            'bulan_3' => $bulan[2],
            'bulan_4' => $bulan[3],
            'bulan_5' => $bulan[4],
            'bulan_6' => $bulan[5],

            'total_patuh' => $hasil['total_patuh'],
            'status_kepatuhan' => MonitoringService::statusKepatuhan($hasil['persen']),
            'is_risk' => $hasil['is_risk'],
            'status_risk' => $hasil['status_risk'],
            'hasil_konseling' => $hasil['materi'],
            'bulan_aktif' => $hasil['total_patuh'],
        ]);

        // =========================
        // 🔥 RESPONSE
        // =========================
        return response()->json([
            'success' => true,
            'message' => 'Monitoring berhasil disimpan',
            'data' => $this->format($monitoring),
        ]);
    }

    /**
     * 🔍 DETAIL MONITORING
     */
    public function show($id)
    {
        $monitoring = Monitoring::with('kehamilan.ibu')->find($id);

        if (!$monitoring) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->format($monitoring) // 🔥 WAJIB
        ]);
    }

    /**
     * ✏️ UPDATE MONITORING
     */
    public function update(Request $request, $id)
    {
        $monitoring = Monitoring::find($id);

        if (!$monitoring) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // 🔥 VALIDASI INPUT
        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'bulan_1' => 'nullable|boolean',
            'bulan_2' => 'nullable|boolean',
            'bulan_3' => 'nullable|boolean',
            'bulan_4' => 'nullable|boolean',
            'bulan_5' => 'nullable|boolean',
            'bulan_6' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 🔥 AMBIL DATA BULAN (REQUEST PRIORITAS, FALLBACK KE DB)
        $bulanCheck = [
            (int) ($request->bulan_1 ?? $monitoring->bulan_1 ?? 0),
            (int) ($request->bulan_2 ?? $monitoring->bulan_2 ?? 0),
            (int) ($request->bulan_3 ?? $monitoring->bulan_3 ?? 0),
            (int) ($request->bulan_4 ?? $monitoring->bulan_4 ?? 0),
            (int) ($request->bulan_5 ?? $monitoring->bulan_5 ?? 0),
            (int) ($request->bulan_6 ?? $monitoring->bulan_6 ?? 0),
        ];

        // 🔥 VALIDASI BERURUTAN (WAJIB)
        for ($i = 1; $i < 6; $i++) {
            if ($bulanCheck[$i] == 1 && $bulanCheck[$i - 1] == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Isi bulan sebelumnya terlebih dahulu"
                ], 422);
            }
        }

        // 🔥 UPDATE DATA DASAR (SETELAH LOLOS VALIDASI)
        $monitoring->update($validator->validated());

        // 🔥 REFRESH DATA TERBARU
        $monitoring->refresh();
        $kehamilan = $monitoring->kehamilan;

        // 🔥 PROSES LOGIC
        $hasil = $this->processMonitoring($kehamilan, $bulanCheck);

        // 🔥 UPDATE HASIL PERHITUNGAN
        $monitoring->update([
            'total_patuh' => $hasil['total_patuh'],
            'status_kepatuhan' => MonitoringService::statusKepatuhan($hasil['persen']),
            'is_risk' => $hasil['is_risk'],
            'status_risk' => $hasil['status_risk'],
            'hasil_konseling' => $hasil['materi'],
            'bulan_aktif' => $hasil['total_patuh'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Monitoring berhasil diupdate',
            'data' => $this->format($monitoring->fresh())
        ]);
    }

    /**
     * 🗑️ DELETE MONITORING
     */
    public function destroy($id)
    {
        $monitoring = Monitoring::find($id);

        if (!$monitoring) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $monitoring->delete();

        return response()->json([
            'success' => true,
            'message' => 'Monitoring berhasil dihapus'
        ]);
    }

    /**
     * 🎯 FORMAT RESPONSE (BIAR RAPI DI FLUTTER)
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

        // 🔥 TAMBAH HITUNG USIA
        $usiaMinggu = 0;
        if (!empty($m->kehamilan?->hpht)) {
            $usiaMinggu = \Carbon\Carbon::parse($m->kehamilan->hpht)
                ->diffInWeeks(now(), false);

            if ($usiaMinggu < 0) $usiaMinggu = 0;
        }

        $statusRisk = 'aman';
        if ($m->is_risk) {
            $statusRisk = $hitung['persen'] < 50 ? 'tinggi' : 'sedang';
        }

        return [
            'id' => $m->id,

            // 🔥 TAMBAH INI
            'kehamilan_id' => $m->kehamilan_id,

            'tanggal' => $m->tanggal,

            'bulan' => $bulan,

            'kepatuhan_persen' => $hitung['persen'],
            'is_risk' => (bool) $m->is_risk,
            'status_risk' => $statusRisk,

            'usia_minggu' => $usiaMinggu,

            'ibu' => [
                'nama' => $m->kehamilan?->ibu?->nama,
                'hpht' => $m->kehamilan?->hpht,
                'alamat' => $m->kehamilan?->ibu?->alamat,
            ],

            'konseling' => $m->hasil_konseling ?? [],
        ];
    }

    public function preview(Request $request)
    {
        $request->validate([
            'kehamilan_id' => 'required|exists:kehamilans,id',
        ]);

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

    public function last($kehamilan_id)
    {
        $last = Monitoring::where('kehamilan_id', $kehamilan_id)
            ->latest()
            ->first();

        // 🔥 HANDLE KOSONG
        if (!$last) {
            return response()->json([
                'success' => true,
                'bulan_terakhir' => 0
            ]);
        }

        $bulan = [
            (int) ($last->bulan_1 ?? 0),
            (int) ($last->bulan_2 ?? 0),
            (int) ($last->bulan_3 ?? 0),
            (int) ($last->bulan_4 ?? 0),
            (int) ($last->bulan_5 ?? 0),
            (int) ($last->bulan_6 ?? 0),
        ];

        $bulanTerakhir = 0;

        foreach ($bulan as $i => $val) {
            if ($val == 1) {
                $bulanTerakhir = $i + 1; // 🔥 FIX DI SINI
            }
        }

        return response()->json([
            'success' => true,
            'bulan_terakhir' => $bulanTerakhir
        ]);
    }
}
