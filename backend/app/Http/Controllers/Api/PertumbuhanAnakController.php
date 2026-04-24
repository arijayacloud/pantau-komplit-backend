<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PertumbuhanAnak;
use App\Models\Anak;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PertumbuhanAnakController extends Controller
{
    /**
     * 📋 LIST DATA (by anak)
     */
    public function index(Request $request)
    {
        $query = PertumbuhanAnak::with('anak');

        // 🔥 filter anak
        if ($request->anak_id) {
            $query->where('anak_id', $request->anak_id);
        }

        // 🔍 SEARCH (tanggal / status)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tanggal', 'like', "%{$request->search}%")
                    ->orWhere('status_bb', 'like', "%{$request->search}%")
                    ->orWhere('status_tb', 'like', "%{$request->search}%");
            });
        }

        // 📅 FILTER TANGGAL
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // 🔥 PAGINATION
        $perPage = $request->per_page ?? 10;

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data->through(fn($item) => $this->format($item)),
        ]);
    }

    /**
     * ➕ SIMPAN DATA
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'tanggal' => 'required|date',
            'berat_badan' => 'nullable|numeric|min:0|max:30',
            'tinggi_badan' => 'nullable|numeric|min:0|max:150',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:60',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 Hitung umur & Z-Score
        $anak = Anak::find($data['anak_id']);
        $umur = $this->hitungUmurBulan($anak->tanggal_lahir, $data['tanggal']);

        $zScore = $this->hitungZScore($umur, $data['berat_badan'], $data['tinggi_badan']);

        $data['z_score_bb'] = $zScore['bb'];
        $data['z_score_tb'] = $zScore['tb'];

        $pertumbuhan = PertumbuhanAnak::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pertumbuhan berhasil disimpan',
            'data' => $this->format($pertumbuhan)
        ], 201);
    }

    /**
     * 🔍 DETAIL
     */
    public function show($id)
    {
        $item = PertumbuhanAnak::with('anak')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->format($item)
        ]);
    }

    /**
     * ✏️ UPDATE
     */
    public function update(Request $request, $id)
    {
        $item = PertumbuhanAnak::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'berat_badan' => 'nullable|numeric|min:0|max:30',
            'tinggi_badan' => 'nullable|numeric|min:0|max:150',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:60',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 VALIDASI TIDAK BOLEH TURUN
        if (isset($data['tinggi_badan']) && $data['tinggi_badan'] < $item->tinggi_badan) {
            return response()->json([
                'success' => false,
                'message' => 'Tinggi badan tidak boleh lebih kecil dari data sebelumnya'
            ], 422);
        }

        if (isset($data['lingkar_kepala']) && $data['lingkar_kepala'] < $item->lingkar_kepala) {
            return response()->json([
                'success' => false,
                'message' => 'Lingkar kepala tidak boleh lebih kecil dari data sebelumnya'
            ], 422);
        }

        // 🔥 Recalculate Z-Score
        $tanggal = $data['tanggal'] ?? $item->tanggal;
        $bb = $data['berat_badan'] ?? $item->berat_badan;
        $tb = $data['tinggi_badan'] ?? $item->tinggi_badan;

        $umur = $this->hitungUmurBulan($item->anak->tanggal_lahir, $tanggal);
        $zScore = $this->hitungZScore($umur, $bb, $tb);

        $data['z_score_bb'] = $zScore['bb'];
        $data['z_score_tb'] = $zScore['tb'];

        $item->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $this->format($item)
        ]);
    }

    /**
     * ❌ DELETE
     */
    public function destroy($id)
    {
        $item = PertumbuhanAnak::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * 🧠 HITUNG UMUR (bulan)
     */
    private function hitungUmurBulan($tglLahir, $tanggal)
    {
        return Carbon::parse($tglLahir)->diffInMonths(Carbon::parse($tanggal));
    }

    /**
     * 📊 HITUNG Z-SCORE (SIMPLE APPROXIMATION)
     * ⚠️ Bisa di-upgrade pakai WHO table
     */
    private function hitungZScore($umur, $bb, $tb)
    {
        $zBB = null;
        $zTB = null;

        $ref = $this->getMedianSD($umur);

        if ($bb && isset($ref['bb'])) {
            [$median, $sd] = $ref['bb'];
            $zBB = round(($bb - $median) / $sd, 2);
        }

        if ($tb && isset($ref['tb'])) {
            [$median, $sd] = $ref['tb'];
            $zTB = round(($tb - $median) / $sd, 2);
        }

        return [
            'bb' => $zBB,
            'tb' => $zTB
        ];
    }

    /**
     * 🎯 FORMAT RESPONSE
     */
    private function format($item)
    {
        return [
            'id' => $item->id,
            'anak' => $item->anak,
            'tanggal' => $item->tanggal,
            'berat_badan' => $item->berat_badan,
            'tinggi_badan' => $item->tinggi_badan,
            'lingkar_kepala' => $item->lingkar_kepala,
            'z_score_bb' => $item->z_score_bb,
            'z_score_tb' => $item->z_score_tb,
            'status_bb' => $this->statusZ($item->z_score_bb),
            'status_tb' => $this->statusZ($item->z_score_tb),
        ];
    }

    /**
     * 🚦 STATUS Z-SCORE
     */
    private function statusZ($z)
    {
        if ($z === null) return '-';

        if ($z < -3) return 'Sangat Kurang';
        if ($z < -2) return 'Kurang';
        if ($z <= 2) return 'Normal';
        return 'Lebih';
    }

    private function getMedianSD($umur)
    {
        // 🔥 contoh data sederhana (0–24 bulan)
        $table = [
            0 => ['bb' => [3.3, 0.5], 'tb' => [50, 2]],
            6 => ['bb' => [7.9, 0.8], 'tb' => [67, 2.5]],
            12 => ['bb' => [9.6, 1], 'tb' => [75, 3]],
            18 => ['bb' => [10.9, 1.1], 'tb' => [82, 3]],
            24 => ['bb' => [12.2, 1.2], 'tb' => [87, 3.2]],
        ];

        // 🔥 cari umur terdekat
        $closest = collect($table)->keys()->sortBy(fn($k) => abs($k - $umur))->first();

        return $table[$closest];
    }

    public function grafik($anak_id)
    {
        $data = PertumbuhanAnak::where('anak_id', $anak_id)
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'bb' => $item->berat_badan,
                    'tb' => $item->tinggi_badan,
                    'z_bb' => $item->z_score_bb,
                    'z_tb' => $item->z_score_tb,
                ];
            })
        ]);
    }
}
