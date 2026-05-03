<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anak;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AnakController extends Controller
{
    /**
     * LIST DATA ANAK
     */
    public function index(Request $request)
    {
        $query = Anak::with(['ibu', 'kehamilan'])->latest();

        // 🔍 SEARCH
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // 🎂 FILTER UMUR (bulan)
        if ($request->umur) {
            if ($request->umur == 'gt12') {
                $query->whereRaw(
                    'TIMESTAMPDIFF(MONTH, tanggal_lahir, NOW()) > 12'
                );
            } else {
                $query->whereRaw(
                    'TIMESTAMPDIFF(MONTH, tanggal_lahir, NOW()) <= ?',
                    [$request->umur]
                );
            }

            // 🔥 FILTER STATUS
            if ($request->status) {
                $query->where('status', $request->status);
            }
        }

        $data = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * SIMPAN DATA ANAK
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'anak_ke' => 'nullable|integer',

            // ✅ sekarang optional semua
            'ibu_id' => 'nullable|exists:ibus,id',
            'nama_ibu' => 'nullable|string|max:255',

            'kehamilan_id' => 'nullable|exists:kehamilans,id',

            'alamat' => 'nullable|string',
            'status' => 'nullable|in:bayi,balita,anak,tidak_aktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        /**
         * 🔥 VALIDASI KEHAMILAN
         * hanya jika ibu_id ADA
         */
        if (!empty($data['ibu_id'])) {

            $kehamilanAktif = \App\Models\Kehamilan::where('ibu_id', $data['ibu_id'])
                ->where('status', 'hamil')
                ->first();

            if ($kehamilanAktif && empty($data['kehamilan_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada kehamilan aktif'
                ], 400);
            }

            if (!empty($data['kehamilan_id'])) {

                $kehamilan = \App\Models\Kehamilan::find($data['kehamilan_id']);

                if (!$kehamilan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data kehamilan tidak ditemukan'
                    ], 404);
                }

                if ($kehamilan->ibu_id != $data['ibu_id']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kehamilan tidak sesuai dengan ibu'
                    ], 400);
                }

                if ($kehamilan->status != 'hamil') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kehamilan sudah tidak aktif'
                    ], 400);
                }

                if (Anak::where('kehamilan_id', $data['kehamilan_id'])->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kehamilan ini sudah memiliki anak'
                    ], 400);
                }
            }
        }

        /**
         * 🔥 AUTO STATUS
         */
        $tanggalLahir = Carbon::parse($data['tanggal_lahir']);
        $umurBulan = $tanggalLahir->diffInMonths(now());

        if (!isset($data['status'])) {
            $data['status'] = $umurBulan <= 12 ? 'bayi'
                : ($umurBulan <= 59 ? 'balita' : 'anak');
        }

        /**
         * 🔥 AUTO ANAK KE
         * hanya kalau ibu_id ADA
         */
        if (!empty($data['ibu_id'])) {
            $data['anak_ke'] = Anak::where('ibu_id', $data['ibu_id'])->count() + 1;
        }

        /**
         * ✅ SIMPAN
         */
        $anak = Anak::create($data);

        /**
         * 🔥 UPDATE KEHAMILAN
         */
        if (!empty($data['kehamilan_id'])) {
            \App\Models\Kehamilan::where('id', $data['kehamilan_id'])
                ->update(['status' => 'selesai']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil disimpan',
            'data' => $anak,
            'usia' => [
                'bulan' => $umurBulan,
                'tahun' => $tanggalLahir->age
            ]
        ], 201);
    }

    /**
     * DETAIL ANAK
     */
    public function show($id)
    {
        $anak = Anak::with([
            'kehamilan',
            'asi',
            'pmba.detail'
        ])->find($id);

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data anak tidak ditemukan'
            ], 404);
        }

        $tanggalLahir = Carbon::parse($anak->tanggal_lahir);

        return response()->json([
            'success' => true,
            'data' => $anak,
            'usia' => [
                'bulan' => $tanggalLahir->diffInMonths(now()),
                'tahun' => $tanggalLahir->age
            ]
        ]);
    }

    /**
     * UPDATE DATA ANAK
     */
    public function update(Request $request, $id)
    {
        $anak = Anak::find($id);

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'sometimes|nullable|string|max:255',
            'nama' => 'sometimes|required|string|max:255',
            'tanggal_lahir' => 'sometimes|required|date',
            'jenis_kelamin' => 'sometimes|required|in:L,P',

            'anak_ke' => 'sometimes|nullable|integer',

            // ✅ sekarang full optional
            'ibu_id' => 'sometimes|nullable|exists:ibus,id',
            'nama_ibu' => 'sometimes|nullable|string|max:255',

            'alamat' => 'nullable|string',
            'status' => 'sometimes|in:bayi,balita,anak,tidak_aktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        /**
         * ❌ HAPUS VALIDASI WAJIB IBU
         * sekarang boleh kosong semua
         */

        /**
         * 🔥 AUTO STATUS (kalau tanggal_lahir diubah)
         */
        if (isset($data['tanggal_lahir']) && !isset($data['status'])) {
            $tanggalLahir = Carbon::parse($data['tanggal_lahir']);
            $umurBulan = $tanggalLahir->diffInMonths(now());

            $data['status'] = $umurBulan <= 12 ? 'bayi'
                : ($umurBulan <= 59 ? 'balita' : 'anak');
        }

        /**
         * 🔥 AUTO ANAK KE (kalau ibu_id berubah)
         */
        if (array_key_exists('ibu_id', $data) && !empty($data['ibu_id'])) {
            $data['anak_ke'] = Anak::where('ibu_id', $data['ibu_id'])
                ->where('id', '!=', $anak->id)
                ->count() + 1;
        }

        /**
         * ✅ UPDATE
         */
        $anak->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diupdate',
            'data' => $anak
        ]);
    }

    /**
     * HAPUS DATA
     */
    public function destroy($id)
    {
        $anak = Anak::find($id);

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $anak->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil dihapus'
        ]);
    }
}
