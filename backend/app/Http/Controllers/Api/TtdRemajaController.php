<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TtdRemaja;
use Illuminate\Support\Facades\Validator;
use App\Services\DecisionRuleEngine;

class TtdRemajaController extends Controller
{
    /**
     * LIST DATA
     */
    public function index(Request $request)
    {
        $query = TtdRemaja::with('remaja')->latest();

        // ======================
        // 🔍 SEARCH (nama remaja)
        // ======================
        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('remaja', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        $query
            ->when(
                $request->filled('remaja_id'),
                fn($q) =>
                $q->where('remaja_id', $request->remaja_id)
            )

            ->when(
                $request->filled('tanggal_from') && $request->filled('tanggal_to'),
                fn($q) =>
                $q->whereBetween('tanggal', [
                    $request->tanggal_from,
                    $request->tanggal_to
                ])
            )

            ->when(
                $request->filled('tanggal_from') && !$request->filled('tanggal_to'),
                fn($q) =>
                $q->whereDate('tanggal', '>=', $request->tanggal_from)
            )

            ->when(
                !$request->filled('tanggal_from') && $request->filled('tanggal_to'),
                fn($q) =>
                $q->whereDate('tanggal', '<=', $request->tanggal_to)
            )

            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'baik') {
                    $q->where('jumlah_minum', '>=', 7);
                } elseif ($request->status === 'cukup') {
                    $q->whereBetween('jumlah_minum', [4, 6]);
                } elseif ($request->status === 'kurang') {
                    $q->where('jumlah_minum', '<', 4);
                }
            });

        // ======================
        // 📄 PAGINATION
        // ======================
        $perPage = $request->get('per_page', 10);

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data TTD remaja berhasil diambil',
            'data' => $data->items(),

            // 🔥 META PAGINATION
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * SIMPAN DATA + KONSELING + SUMMARY
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remaja_id' => 'required|exists:remaja_putris,id',
            'tanggal' => 'required|date',
            'jumlah_minum' => 'required|integer|min:0|max:7',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 CEK DUPLIKAT
        $exists = TtdRemaja::where('remaja_id', $data['remaja_id'])
            ->where('tanggal', $data['tanggal'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data tanggal ini sudah diinput'
            ], 409);
        }

        $ttd = TtdRemaja::create($data);

        // ======================
        // 🔥 HITUNG DATA AGREGAT
        // ======================
        $all = TtdRemaja::where('remaja_id', $data['remaja_id'])->get();

        $total = $all->count();
        $patuh = $all->where('jumlah_minum', '>=', 4)->count();
        $tidak = $all->where('jumlah_minum', '<', 4)->count();

        $persen = $total > 0 ? round(($patuh / $total) * 100) : 0;

        // ======================
        // 🔥 PREPARE INPUT RULE ENGINE
        // ======================
        $ruleInput = [
            'jumlah_minum' => $data['jumlah_minum'],
            'kepatuhan_persen' => $persen,
            'total_patuh' => $patuh,
            'total_tidak' => $tidak,
        ];

        // ======================
        // 🔥 JALANKAN RULE ENGINE
        // ======================
        $engine = new DecisionRuleEngine();
        $result = $engine->run('ttd_remaja', $ruleInput);

        // ======================
        // 🔥 FORMAT OUTPUT
        // ======================
        $konseling = collect($result['hasil'])
            ->pluck('isi')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Data TTD remaja berhasil disimpan',
            'data' => $ttd,

            // 🔥 AI RESULT
            'kepatuhan_persen' => $persen,
            'score' => $result['score'],
            'is_risk' => $result['is_risk'],
            'konseling' => $konseling
        ], 201);
    }

    /**
     * DETAIL
     */
    public function show($id)
    {
        $data = TtdRemaja::with('remaja')->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $ttd = TtdRemaja::find($id);

        if (!$ttd) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'sometimes|date',
            'jumlah_minum' => 'sometimes|integer|min:0|max:7',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // ======================
        // 🔥 UPDATE DATA
        // ======================
        $ttd->update($validator->validated());

        // ======================
        // 🔥 HITUNG ULANG AGREGAT
        // ======================
        $all = TtdRemaja::where('remaja_id', $ttd->remaja_id)->get();

        $total = $all->count();
        $patuh = $all->where('jumlah_minum', '>=', 4)->count();
        $tidak = $all->where('jumlah_minum', '<', 4)->count();

        $persen = $total > 0 ? round(($patuh / $total) * 100) : 0;

        // ======================
        // 🔥 PREPARE RULE ENGINE INPUT
        // ======================
        $ruleInput = [
            'jumlah_minum' => $ttd->jumlah_minum,
            'kepatuhan_persen' => $persen,
            'total_patuh' => $patuh,
            'total_tidak' => $tidak,
        ];

        // ======================
        // 🔥 RUN RULE ENGINE
        // ======================
        $engine = new DecisionRuleEngine();
        $result = $engine->run('ttd_remaja', $ruleInput);

        // ======================
        // 🔥 FORMAT OUTPUT
        // ======================
        $konseling = collect($result['hasil'])
            ->pluck('isi')
            ->values();

        // ======================
        // 🔥 STATUS (OPTIONAL)
        // ======================
        $status = match (true) {
            $persen >= 80 => 'Baik',
            $persen >= 50 => 'Cukup',
            default => 'Kurang',
        };

        return response()->json([
            'success' => true,
            'message' => 'Data TTD remaja berhasil diupdate',
            'data' => $ttd,

            // 🔥 AI RESULT
            'kepatuhan_persen' => $persen,
            'status' => $status,
            'score' => $result['score'],
            'is_risk' => $result['is_risk'],
            'konseling' => $konseling
        ]);
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $ttd = TtdRemaja::find($id);

        if (!$ttd) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $ttd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data TTD remaja berhasil dihapus'
        ]);
    }

    public function preview(Request $request, DecisionRuleEngine $engine)
    {
        $validator = Validator::make($request->all(), [
            'remaja_id' => 'required|exists:remaja_putris,id',
            'tanggal' => 'required|date',
            'jumlah_minum' => 'required|integer|min:0|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $remajaId = $request->remaja_id;
        $jumlahBaru = $request->jumlah_minum;

        // 🔥 DATA HISTORI
        $dataLama = TtdRemaja::where('remaja_id', $remajaId)->get();

        $total = $dataLama->count() + 1;
        $patuh = $dataLama->where('jumlah_minum', '>=', 4)->count();

        if ($jumlahBaru >= 4) {
            $patuh++;
        }

        $persen = $total > 0 ? round(($patuh / $total) * 100) : 0;

        $totalTidak = $total - $patuh;

        // 🔥 DATA UNTUK ENGINE
        $input = [
            'jumlah_minum' => $jumlahBaru,
            'kepatuhan_persen' => $persen,
            'total_tidak' => $totalTidak,
            'total_patuh' => $patuh,
        ];

        // 🔥 RUN RULE ENGINE
        $result = $engine->run('ttd_remaja', $input);

        // 🔥 STATUS DARI SCORE
        $status = match (true) {
            $result['score'] >= 10 => 'Kurang',
            $result['score'] >= 5 => 'Cukup',
            default => 'Baik',
        };

        return response()->json([
            'success' => true,
            'data' => [
                'kepatuhan_persen' => $persen,
                'status' => $status,
                'is_risk' => $result['is_risk'],
                'score' => $result['score'],
                'konseling' => collect($result['hasil'])->pluck('isi'),
            ]
        ]);
    }
}
