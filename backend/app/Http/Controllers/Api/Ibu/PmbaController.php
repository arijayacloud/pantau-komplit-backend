<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pmba;
use App\Models\Anak;
use App\Models\Ibu;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\KonselingService;
use App\Services\PmbaService;
use Carbon\Carbon;

class PmbaController extends Controller
{
    protected PmbaService $pmbaService;

    public function __construct(PmbaService $pmbaService)
    {
        $this->pmbaService = $pmbaService;
    }

    /**
     * 🔑 Ambil data ibu dari user login
     */
    private function getIbu()
    {
        return Ibu::where('user_id', Auth::id())->first();
    }

    /**
     * 📋 LIST DATA PMBA MILIK IBU
     */
    public function index(Request $request)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound('Data ibu tidak ditemukan');
        }

        $query = Pmba::with(['detail', 'anak'])
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibu->id))
            ->latest();

        if ($request->anak_id) {
            $query->where('anak_id', $request->anak_id);
        }

        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->search) {
            $query->whereHas('anak', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * 💾 STORE
     */
    public function store(Request $request)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound('Data ibu tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'tanggal' => 'required|date',
            'frekuensi_makan' => 'required|integer|min:1|max:10',
            'tekstur' => 'required|in:lumat,lembek,padat',
            'porsi' => 'required|in:kurang,cukup',
            'sumber_makanan' => 'required|in:rumahan,instan,campuran',

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
            $result = $this->pmbaService->store(
                $validator->validated(),
                $ibu->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $result['pmba'],
                'konseling' => $result['konseling'],
                'who' => $result['who']
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * 🔍 DETAIL
     */
    public function show($id)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound();
        }

        $pmba = Pmba::with(['detail', 'anak'])
            ->where('id', $id)
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibu->id))
            ->first();

        if (!$pmba) {
            return $this->notFound();
        }

        $dataGabungan = array_merge(
            $pmba->toArray(),
            $pmba->detail?->toArray() ?? []
        );

        $catatan = array_merge(
            KonselingService::generate($dataGabungan),
            KonselingService::fromDatabase($dataGabungan, $pmba->tipe)
        );

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
     * ✏️ UPDATE
     */
    public function update(Request $request, $id)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound();
        }

        try {
            $result = $this->pmbaService->updateByIbu(
                $ibu->id,
                $id,
                $request->all()
            );

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * 🗑️ DELETE
     */
    public function destroy($id)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound();
        }

        $deleted = $this->pmbaService->deleteByIbu($ibu->id, $id);

        if (!$deleted) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * 🔮 PREVIEW (tanpa simpan)
     */
    public function preview(Request $request)
    {
        $ibu = $this->getIbu();

        if (!$ibu) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'frekuensi_makan' => 'required|integer|min:1|max:10',
            'tekstur' => 'required|in:lumat,lembek,padat',
            'porsi' => 'required|in:kurang,cukup',
            'sumber_makanan' => 'required|in:rumahan,instan,campuran',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $anak = Anak::where('id', $request->anak_id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $data = $request->all();
        $data['anak'] = $anak;

        $usia = Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());
        $data['usia_bulan'] = max($usia, 0);

        $mdd = KonselingService::hitungMDD($data);
        $mmf = KonselingService::hitungMMF($data);
        $mad = KonselingService::hitungMAD($data);

        $catatan = array_merge(
            KonselingService::generate($data),
            KonselingService::fromDatabase($data, 'pmba')
        );

        return response()->json([
            'success' => true,
            'konseling' => array_values(array_unique($catatan)),
            'who' => [
                'mdd_score' => $mdd['total_group'] ?? 0,
                'mmf_status' => $mmf['status'] ?? '-',
                'mad_status' => $mad['status'] ?? '-',
            ]
        ]);
    }

    private function notFound($msg = 'Data tidak ditemukan')
    {
        return response()->json([
            'success' => false,
            'message' => $msg
        ], 404);
    }

    private function error($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan',
            'error' => $e->getMessage()
        ], 500);
    }
}
