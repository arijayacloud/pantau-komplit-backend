<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsiEksklusif;
use App\Models\Anak;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\DecisionRuleEngine;

class AsiController extends Controller
{
    protected DecisionRuleEngine $engine;

    public function __construct(DecisionRuleEngine $engine)
    {
        $this->engine = $engine;
    }

    /*
    |--------------------------------------------------------------------------
    | LIST DATA
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = AsiEksklusif::with('anak')
            ->orderBy('anak_id')
            ->orderBy('bulan_ke');

        if ($request->filled('search')) {
            $query->whereHas('anak', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('anak_id')) {
            $query->where('anak_id', $request->anak_id);
        }

        if ($request->filled('bulan')) {
            if ($request->bulan == 6) {
                $query->where('bulan_ke', '<=', 6);
            } else {
                $query->where('bulan_ke', '>', 6);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(10)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anak_id'     => 'required|exists:anaks,id',
            'bulan_ke'    => 'required|integer|min:1|max:24',
            'status_asi'  => 'required|boolean',
            'catatan'     => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $data = $validator->validated();

        $exists = AsiEksklusif::where('anak_id', $data['anak_id'])
            ->where('bulan_ke', $data['bulan_ke'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data bulan ini sudah ada'
            ], 409);
        }

        DB::beginTransaction();

        try {

            $asi = AsiEksklusif::create($data);

            $analysis = $this->analyzeAsi(
                $data['anak_id'],
                $data['bulan_ke']
            );

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Data ASI berhasil disimpan',
                'data'      => $asi->load('anak'),
                'status'    => $analysis['status'],
                'score'     => $analysis['score'],
                'risk_score' => $analysis['risk_score'],
                'konseling' => $analysis['konseling'],
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->serverError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $data = AsiEksklusif::with('anak')->find($id);

        if (!$data) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $asi = AsiEksklusif::find($id);

        if (!$asi) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'status_asi' => 'required|boolean',
            'catatan'    => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        DB::beginTransaction();

        try {

            $asi->update($validator->validated());

            $analysis = $this->analyzeAsi(
                $asi->anak_id,
                $asi->bulan_ke
            );

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Data berhasil diperbarui',
                'data'      => $asi->fresh()->load('anak'),
                'status'    => $analysis['status'],
                'score'     => $analysis['score'],
                'risk_score' => $analysis['risk_score'],
                'konseling' => $analysis['konseling'],
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->serverError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $asi = AsiEksklusif::find($id);

        if (!$asi) {
            return $this->notFound();
        }

        $asi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PREVIEW
    |--------------------------------------------------------------------------
    */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anak_id'    => 'required|exists:anaks,id',
            'bulan_ke'   => 'required|integer|min:1|max:24',
            'status_asi' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $data = $validator->validated();

        $analysis = $this->previewAnalyze($data);

        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MAIN ANALYSIS
    |--------------------------------------------------------------------------
    */
    private function analyzeAsi($anakId, $bulanKe)
    {
        $anak = Anak::find($anakId);

        $riwayat = AsiEksklusif::where('anak_id', $anakId)
            ->where('bulan_ke', '<=', $bulanKe)
            ->orderBy('bulan_ke')
            ->pluck('status_asi')
            ->map(fn($v) => $v == 1)
            ->toArray();

        return $this->runAnalysis(
            $anak,
            $anakId,
            $bulanKe,
            $riwayat
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PREVIEW ANALYSIS
    |--------------------------------------------------------------------------
    */
    private function previewAnalyze($data)
    {
        $anak = Anak::find($data['anak_id']);

        $riwayat = AsiEksklusif::where('anak_id', $data['anak_id'])
            ->where('bulan_ke', '<', $data['bulan_ke'])
            ->orderBy('bulan_ke')
            ->pluck('status_asi')
            ->map(fn($v) => $v == 1)
            ->toArray();

        $riwayat[] = (bool)$data['status_asi'];

        return $this->runAnalysis(
            $anak,
            $data['anak_id'],
            $data['bulan_ke'],
            $riwayat
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RUN ALL ENGINE
    |--------------------------------------------------------------------------
    */
    private function runAnalysis($anak, $anakId, $bulanKe, $riwayat)
    {
        $totalData = count($riwayat);
        $totalAsi  = collect($riwayat)->filter(fn($v) => $v)->count();

        $konsistensi = $totalData > 0
            ? round($totalAsi / $totalData, 2)
            : 1;

        $score = $this->calculateAsiScore($riwayat, $bulanKe);

        $engineData = [
            'anak_id'       => $anakId,
            'bulan_ke'      => $bulanKe,
            'usia_bulan'    => $anak?->usia_bulan ?? $bulanKe,
            'status_asi'    => end($riwayat),
            'riwayat_asi'   => $riwayat,
            'total_asi'     => $totalAsi,
            'total_data'    => $totalData,
            'konsistensi'   => $konsistensi,
            'score'         => $score,
            'target_bulan'  => 6,
            'is_complete'   => $bulanKe >= 6,
            'is_under_6'    => $bulanKe <= 6,
            'asi_terputus'  => in_array(false, $riwayat),
        ];

        $result = $this->engine->run('asi', $engineData);

        return [
            'status'       => $this->getStatus($score),
            'score'        => $score,
            'risk_score'   => $result['risk_score'] ?? 0,
            'konseling'    => collect($result['hasil'])
                ->pluck('isi')
                ->values(),
            'konsistensi' => $konsistensi,
            'bulan_ke' => $bulanKe,
            'total_asi' => $totalAsi
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SCORING SYSTEM
    |--------------------------------------------------------------------------
    */
    private function calculateAsiScore($riwayat, $bulanKe)
    {
        $totalData = count($riwayat);
        $totalAsi  = collect($riwayat)->filter(fn($v) => $v)->count();

        $konsistensi = $totalData > 0
            ? $totalAsi / $totalData
            : 1;

        $coverage = ($totalAsi / 6) * 40;

        $scoreKonsistensi = $konsistensi * 30;

        $putusCount = collect($riwayat)
            ->filter(fn($v) => !$v)
            ->count();

        if ($putusCount == 0) {
            $kontinuitas = 20;
        } elseif ($putusCount <= 2) {
            $kontinuitas = 10;
        } else {
            $kontinuitas = 0;
        }

        $penalty = 0;

        if ($bulanKe < 6 && end($riwayat) === false) {
            $penalty = 10;
        }

        $score = $coverage +
            $scoreKonsistensi +
            $kontinuitas -
            $penalty;

        return max(0, min(100, round($score)));
    }

    private function getStatus($score)
    {
        if ($score >= 80) return 'Aman';
        if ($score >= 60) return 'Perlu Perhatian';
        return 'Risiko';
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSE HELPER
    |--------------------------------------------------------------------------
    */
    private function validationError($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    private function notFound()
    {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    private function serverError($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server',
            'error' => $e->getMessage()
        ], 500);
    }
}
