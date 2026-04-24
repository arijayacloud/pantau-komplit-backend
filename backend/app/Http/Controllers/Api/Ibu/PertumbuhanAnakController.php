<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PertumbuhanAnak;
use App\Models\Anak;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PertumbuhanAnakController extends Controller
{
    /**
     * 📋 LIST DATA (HANYA ANAK MILIK IBU)
     */
    private function getIbu()
    {
        return \App\Models\Ibu::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $ibu = $this->getIbu();

        if (!$ibu) return $this->forbidden();

        $query = PertumbuhanAnak::with('anak')
            ->whereHas('anak', function ($q) use ($ibu) {
                $q->where('ibu_id', $ibu->id); // ✅ FIX
            });

        if ($request->anak_id) {
            $query->where('anak_id', $request->anak_id);
        }

        $data = $query->latest()->paginate(10);

        $data->getCollection()->transform(fn($item) => $this->format($item));

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * ➕ SIMPAN DATA
     */
    public function store(Request $request)
    {
        $ibu = $this->getIbu();
        if (!$ibu) return $this->forbidden();

        $validator = Validator::make($request->all(), [
            'anak_id' => 'required|exists:anaks,id',
            'tanggal' => 'required|date',
            'berat_badan' => 'nullable|numeric|min:0|max:30',
            'tinggi_badan' => 'nullable|numeric|min:0|max:150',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:60',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $data = $validator->validated();

        // ✅ FIX DI SINI
        $anak = Anak::where('id', $data['anak_id'])
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$anak) {
            return $this->forbidden();
        }

        $umur = $this->hitungUmurBulan($anak->tanggal_lahir, $data['tanggal']);
        $zScore = $this->hitungZScore($umur, $data['berat_badan'], $data['tinggi_badan']);

        $data['z_score_bb'] = $zScore['bb'];
        $data['z_score_tb'] = $zScore['tb'];

        $pertumbuhan = PertumbuhanAnak::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $this->format($pertumbuhan->load('anak'))
        ], 201);
    }

    /**
     * 🔍 DETAIL
     */
    public function show($id)
    {
        $ibu = $this->getIbu();
        if (!$ibu) return $this->forbidden();

        $item = PertumbuhanAnak::with('anak')
            ->where('id', $id)
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibu->id)) // ✅ FIX
            ->first();

        if (!$item) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'data' => $this->format($item)
        ]);
    }

    /**
     * ✏️ UPDATE (TERBATAS)
     */
    public function update(Request $request, $id)
    {
        $ibu = $this->getIbu();

        $item = PertumbuhanAnak::with('anak')
            ->where('id', $id)
            ->whereHas('anak', fn($q) => $q->where('ibu_id', $ibu->id)) // ✅ FIX
            ->first();

        if (!$item) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'nullable|date',
            'berat_badan' => 'nullable|numeric|min:0|max:30',
            'tinggi_badan' => 'nullable|numeric|min:0|max:150',
            'lingkar_kepala' => 'nullable|numeric|min:0|max:60',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $data = $validator->validated();

        // 🔥 VALIDASI LOGIKA MEDIS SEDERHANA
        if (isset($data['tinggi_badan']) && $data['tinggi_badan'] < $item->tinggi_badan) {
            return response()->json([
                'success' => false,
                'message' => 'Tinggi badan tidak boleh turun'
            ], 422);
        }

        if (isset($data['lingkar_kepala']) && $data['lingkar_kepala'] < $item->lingkar_kepala) {
            return response()->json([
                'success' => false,
                'message' => 'Lingkar kepala tidak boleh turun'
            ], 422);
        }

        // 🔥 HITUNG ULANG Z-SCORE
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
            'data' => $this->format($item->fresh())
        ]);
    }

    /**
     * 📊 GRAFIK
     */
    public function grafik($anak_id)
    {
        $ibu = $this->getIbu();
        if (!$ibu) return $this->forbidden();

        $anak = Anak::where('id', $anak_id)
            ->where('ibu_id', $ibu->id) // ✅ FIX
            ->first();

        if (!$anak) {
            return $this->forbidden();
        }

        $data = PertumbuhanAnak::where('anak_id', $anak_id)
            ->orderBy('tanggal')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(fn($item) => [
                'tanggal' => $item->tanggal,
                'bb' => $item->berat_badan,
                'tb' => $item->tinggi_badan,
                'z_bb' => $item->z_score_bb,
                'z_tb' => $item->z_score_tb,
            ])
        ]);
    }

    // =========================
    // 🔧 HELPER
    // =========================

    private function hitungUmurBulan($tglLahir, $tanggal)
    {
        return Carbon::parse($tglLahir)
            ->diffInMonths(Carbon::parse($tanggal));
    }

    private function hitungZScore($umur, $bb, $tb)
    {
        $ref = $this->getMedianSD($umur);

        return [
            'bb' => $bb ? round(($bb - $ref['bb'][0]) / $ref['bb'][1], 2) : null,
            'tb' => $tb ? round(($tb - $ref['tb'][0]) / $ref['tb'][1], 2) : null,
        ];
    }

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
        return [
            'bb' => [9.6, 1],
            'tb' => [75, 3],
        ];
    }

    private function validationError($validator)
    {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    private function forbidden()
    {
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak'
        ], 403);
    }

    private function notFound()
    {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
}
