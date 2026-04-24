<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pmba;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\KonselingService;
use App\Services\PmbaService;
use App\Models\Anak;
use Carbon\Carbon;

class PmbaController extends Controller
{
    private $pmbaService;

    public function __construct(PmbaService $pmbaService)
    {
        $this->pmbaService = $pmbaService;
    }

    /**
     * List data PMBA
     */
    public function index(Request $request)
    {
        $query = Pmba::with(['detail', 'anak'])
            ->latest();

        // 🔍 FILTER ANAK
        if ($request->anak_id) {
            $query->where('anak_id', $request->anak_id);
        }

        // 🔍 FILTER TANGGAL
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // 🔍 SEARCH (nama anak)
        if ($request->search) {
            $query->whereHas('anak', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        // 🔥 PAGINATION DINAMIS (default 10)
        $perPage = $request->per_page ?? 10;

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data PMBA berhasil diambil',
            'data' => $data->items(),

            // 🔥 META PAGINATION (WAJIB buat Flutter)
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Simpan data PMBA + Konseling otomatis
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'tanggal' => 'required|date',
            'frekuensi_makan' => 'required|integer|min:1|max:10',
            'tekstur' => 'required|in:lumat,lembek,padat',
            'porsi' => 'required|in:kurang,cukup',
            'sumber_makanan' => 'required|in:rumahan,instan,campuran',

            // ❗ FIX DI SINI
            'karbohidrat' => 'boolean',
            'protein_hewani' => 'boolean',
            'protein_nabati' => 'boolean',
            'sayur' => 'boolean',
            'buah' => 'boolean',

            'kacang' => 'nullable|boolean',
            'susu' => 'nullable|boolean',
            'telur' => 'nullable|boolean',
            'vitamin_a' => 'nullable|boolean',
            'asi' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pmbaService->store($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data PMBA berhasil disimpan',
                'data' => $result['pmba'],
                'konseling' => $result['konseling'] ?? [],
                'who' => $result['who']
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail PMBA
     */
    public function show($id)
    {
        $pmba = Pmba::with(['detail', 'anak'])->find($id);

        if (!$pmba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // =====================
        // Gabungkan data
        // =====================
        $dataGabungan = array_merge(
            $pmba->toArray(),
            $pmba->detail ? $pmba->detail->toArray() : []
        );

        // =====================
        // Generate konseling
        // =====================
        $generate = KonselingService::generate($dataGabungan) ?? [];
        $rules = KonselingService::fromDatabase($dataGabungan, $pmba->tipe) ?? [];

        $catatan = array_merge($generate, $rules);
        $dataGabungan['usia_bulan'] = $pmba->usia_bulan ?? 0;

        return response()->json([
            'success' => true,
            'data' => $pmba,
            'konseling' => array_values(array_unique($catatan)),
            'who' => [
                'mdd_score' => $pmba->detail->mdd_score ?? 0,
                'mmf_status' => $pmba->detail->mmf_status ?? null,
                'mad_status' => $pmba->detail->mad_status ?? null,
            ]
        ]);
    }

    /**
     * Update PMBA
     */
    public function update(Request $request, $id)
    {
        $pmba = Pmba::with(['detail', 'anak'])->find($id);

        if (!$pmba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'sometimes|date',
            'frekuensi_makan' => 'sometimes|integer|min:1|max:10',
            'tekstur' => 'sometimes|in:lumat,lembek,padat',
            'porsi' => 'sometimes|in:kurang,cukup',
            'sumber_makanan' => 'required|in:rumahan,instan,campuran',

            'karbohidrat' => 'sometimes|boolean',
            'protein_hewani' => 'sometimes|boolean',
            'protein_nabati' => 'sometimes|boolean',
            'sayur' => 'sometimes|boolean',
            'buah' => 'sometimes|boolean',

            'kacang' => 'nullable|boolean',
            'susu' => 'nullable|boolean',
            'telur' => 'nullable|boolean',
            'vitamin_a' => 'nullable|boolean',
            'asi' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $validated = $validator->validated();

            // =====================
            // Pisahkan data header/detail
            // =====================
            $headerFields = [
                'tanggal',
                'frekuensi_makan',
                'tekstur',
                'porsi',
                'sumber_makanan',
            ];

            $detailFields = [
                'karbohidrat',
                'protein_hewani',
                'protein_nabati',
                'sayur',
                'buah',
                'kacang',
                'susu',
                'telur',
                'vitamin_a',
                'asi'
            ];

            $headerData = array_intersect_key(
                $validated,
                array_flip($headerFields)
            );

            $detailData = array_intersect_key(
                $validated,
                array_flip($detailFields)
            );

            // =====================
            // Update header
            // =====================
            if (!empty($headerData)) {
                $pmba->update($headerData);
            }

            $anak = $pmba->anak;

            $usiaBulan = 0;
            if (!empty($anak?->tanggal_lahir)) {
                $usiaBulan = Carbon::parse($anak->tanggal_lahir)
                    ->diffInMonths(Carbon::parse($pmba->tanggal), false);

                if ($usiaBulan < 0) $usiaBulan = 0;
            }

            $tipe = $usiaBulan >= 6 ? 'mpasi' : 'pmba';

            $pmba->update([
                'usia_bulan' => $usiaBulan,
                'tipe' => $tipe
            ]);

            // =====================
            // Update detail
            // =====================
            if ($pmba->detail && !empty($detailData)) {
                $pmba->detail->update($detailData);
            }

            // reload data terbaru
            $pmba->refresh()->load(['detail', 'anak']);

            // =====================
            // Gabungkan data untuk analisis ulang
            // =====================
            $dataGabungan = array_merge(
                $pmba->toArray(),
                $pmba->detail ? $pmba->detail->toArray() : []
            );

            // =====================
            // Hitung ulang indikator WHO
            // =====================
            $mdd = KonselingService::hitungMDD($dataGabungan);
            $mmf = KonselingService::hitungMMF($dataGabungan);
            $mad = KonselingService::hitungMAD($dataGabungan);
            $scoring = KonselingService::scoring($dataGabungan);

            // =====================
            // Simpan hasil analisis
            // =====================
            if ($pmba->detail) {
                $pmba->detail->update([
                    'skor' => $scoring['skor'],
                    'status' => $scoring['status'],
                    'mdd_score' => $mdd['total_group'],
                    'mmf_status' => $mmf['status'],
                    'mad_status' => $mad['status'],
                ]);
            }

            // =====================
            // Generate konseling
            // =====================
            $catatan = array_merge(
                KonselingService::generate($dataGabungan),
                KonselingService::fromDatabase(
                    $dataGabungan,
                    $pmba->tipe
                )
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => $pmba->fresh()->load(['detail', 'anak']),
                'konseling' => array_values(array_unique($catatan ?? [])),
                'who' => [
                    'mdd_score' => $mdd['total_group'],
                    'mmf_status' => $mmf['status'],
                    'mad_status' => $mad['status'],
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal update',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus PMBA
     */
    public function destroy($id)
    {
        $pmba = Pmba::find($id);

        if (!$pmba) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $pmba->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Preview konseling tanpa simpan
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'frekuensi_makan' => 'required|integer|min:1|max:10',
            'tekstur' => 'required|in:lumat,lembek,padat',
            'porsi' => 'required|in:kurang,cukup',
            'sumber_makanan' => 'required|in:rumahan,instan,campuran',

            // ❗ FIX DI SINI JUGA
            'karbohidrat' => 'boolean',
            'protein_hewani' => 'boolean',
            'protein_nabati' => 'boolean',
            'sayur' => 'boolean',
            'buah' => 'boolean',

            'kacang' => 'nullable|boolean',
            'susu' => 'nullable|boolean',
            'telur' => 'nullable|boolean',
            'vitamin_a' => 'nullable|boolean',
            'asi' => 'nullable|boolean',
            'tipe' => 'nullable|in:pmba,mpasi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $anak = Anak::find($data['anak_id']);
        $data['anak'] = $anak;

        // 🔥 TAMBAHKAN DI SINI
        $usiaBulan = 0;
        if (!empty($anak?->tanggal_lahir)) {
            $usiaBulan = Carbon::parse($anak->tanggal_lahir)
                ->diffInMonths(now(), false);

            if ($usiaBulan < 0) $usiaBulan = 0;
        }

        $data['usia_bulan'] = $usiaBulan;

        $mdd = KonselingService::hitungMDD($data);
        $mmf = KonselingService::hitungMMF($data);
        $mad = KonselingService::hitungMAD($data);

        $generate = KonselingService::generate($data) ?? [];
        $rules = KonselingService::fromDatabase(
            $data,
            $data['tipe'] ?? 'pmba'
        ) ?? [];

        $catatan = array_merge($generate, $rules);

        return response()->json([
            'success' => true,
            'konseling' => array_values(array_unique($catatan ?? [])),
            'who' => [
                'mdd_score' => $mdd['total_group'] ?? 0,
                'mmf_status' => $mmf['status'] ?? '-',
                'mad_status' => $mad['status'] ?? '-',
            ]
        ]);
    }
}
