<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TtdIbu;
use Illuminate\Support\Facades\Validator;

class TtdIbuController extends Controller
{
    /**
     * List data TTD Ibu
     */
    public function index()
    {
        $data = TtdIbu::with('ibu')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data TTD ibu berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * Simpan data TTD + KONSELING
     */
    public function store(Request $request)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'ibu_id' => 'required|exists:ibu_hamils,id',
            'tanggal_dapat' => 'nullable|date',
            'bulan_ke' => 'required|integer|min:1|max:9',
            'jumlah_diminum' => 'required|integer|min:0|max:60',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = TtdIbu::create($validator->validated());

        // ======================
        // KONSELING OTOMATIS
        // ======================
        $konseling = [];

        if ($request->jumlah_diminum < 30) {
            $konseling[] = "Kepatuhan minum TTD kurang";
        }

        if ($request->jumlah_diminum >= 30) {
            $konseling[] = "Kepatuhan minum TTD baik";
        }

        if ($request->bulan_ke >= 7 && $request->jumlah_diminum < 30) {
            $konseling[] = "Perlu peningkatan konsumsi TTD pada trimester akhir";
        }

        return response()->json([
            'success' => true,
            'message' => 'Data TTD berhasil disimpan',
            'data' => $data,
            'konseling' => $konseling
        ], 201);
    }

    /**
     * Detail TTD Ibu
     */
    public function show($id)
    {
        $data = TtdIbu::with('ibu')->find($id);

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
     * Update data TTD
     */
    public function update(Request $request, $id)
    {
        $ttd = TtdIbu::find($id);

        if (!$ttd) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // VALIDASI
        $validator = Validator::make($request->all(), [
            'bulan_ke' => 'sometimes|integer|min:1|max:9',
            'jumlah_diminum' => 'sometimes|integer|min:0|max:60',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $ttd->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data TTD berhasil diupdate',
            'data' => $ttd
        ]);
    }

    /**
     * Hapus data TTD
     */
    public function destroy($id)
    {
        $ttd = TtdIbu::find($id);

        if (!$ttd) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $ttd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data TTD berhasil dihapus'
        ]);
    }
}