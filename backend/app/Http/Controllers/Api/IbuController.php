<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ibu;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class IbuController extends Controller
{
    /**
     * LIST IBU
     */
    public function index(Request $request)
    {
        $query = Ibu::with(['anak'])
            ->withCount(['anak', 'kehamilan'])
            ->latest();

        /// 🔍 SEARCH NAMA
        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        /// 📍 FILTER ALAMAT
        if ($request->alamat) {
            $query->where('alamat', 'like', "%{$request->alamat}%");
        }

        /// 📊 FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->status) {
            $query->whereIn('status', [
                'calon_ibu',
                'hamil',
                'menyusui',
                'tidak_aktif'
            ])->where('status', $request->status);
        }

        $data = $query->paginate(10)->withQueryString();

        $data->getCollection()->transform(function ($item) {
            $item->status = $this->generateStatus($item);

            // 🔥 FLAG KHUSUS
            $item->is_hamil = $item->kehamilan()
                ->where('status', 'hamil')
                ->exists();

            return $item;
        });
        return response()->json([
            'success' => true,
            'message' => 'Data ibu berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * SIMPAN IBU
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:20',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',

            // ❌ HAPUS INI
            // 'status' => ...

            'pendidikan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();

        // 🔥 DEFAULT STATUS
        $data['status'] = 'calon_ibu';

        $ibu = Ibu::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data ibu berhasil disimpan',
            'data' => $ibu
        ], 201);
    }

    /**
     * DETAIL IBU
     */
    public function show($id)
    {
        $ibu = Ibu::with(['anak', 'kehamilan'])
            ->withCount(['anak', 'kehamilan'])
            ->find($id);

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // 🔥 SYNC STATUS REALTIME (optional tapi bagus)
        $ibu->status = $this->generateStatus($ibu);

        return response()->json([
            'success' => true,
            'data' => $ibu
        ]);
    }

    /**
     * UPDATE IBU
     */
    public function update(Request $request, $id)
    {
        $ibu = Ibu::with(['anak', 'kehamilan'])->find($id);

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'sometimes|string|max:20',
            'nama' => 'sometimes|required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',

            // ❌ STATUS TIDAK BOLEH DIINPUT SEMBARANG
            // 'status' => ...

            'pendidikan' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        $ibu->update($data);

        // 🔥 RECALCULATE STATUS OTOMATIS
        $ibu->status = $this->generateStatus($ibu);
        $ibu->save();

        return response()->json([
            'success' => true,
            'message' => 'Data ibu berhasil diupdate',
            'data' => $ibu
        ]);
    }

    /**
     * HAPUS IBU (SOFT DELETE)
     */
    public function destroy($id)
    {
        $ibu = Ibu::find($id);

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $ibu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data ibu berhasil dihapus'
        ]);
    }

    private function generateStatus($ibu)
    {
        // 🔥 CEK KEHAMILAN AKTIF
        $hamil = $ibu->kehamilan()
            ->where('status', 'hamil')
            ->exists();

        if ($hamil) return 'hamil';

        // 🔥 CEK ANAK (<= 24 bulan)
        foreach ($ibu->anak as $anak) {
            $umurBulan = \Carbon\Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());

            if ($umurBulan <= 24) {
                return 'menyusui';
            }
        }

        return 'calon_ibu';
    }
}
