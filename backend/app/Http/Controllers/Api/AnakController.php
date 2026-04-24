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

            'ibu_id' => 'required|exists:ibus,id',
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

        // 🔥 CEK KEHAMILAN AKTIF (status = hamil)
        $kehamilanAktif = \App\Models\Kehamilan::where('ibu_id', $data['ibu_id'])
            ->where('status', 'hamil')
            ->first();

        if ($kehamilanAktif && empty($data['kehamilan_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada kehamilan aktif, tidak bisa menambah anak tanpa menyelesaikan kehamilan'
            ], 400);
        }

        // 🔥 JIKA MENGGUNAKAN KEHAMILAN_ID
        if (!empty($data['kehamilan_id'])) {

            $kehamilan = \App\Models\Kehamilan::find($data['kehamilan_id']);

            // ❌ Pastikan kehamilan milik ibu yang sama
            if ($kehamilan->ibu_id != $data['ibu_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehamilan tidak sesuai dengan ibu'
                ], 400);
            }

            // ❌ Tidak boleh kalau sudah selesai / gugur
            if ($kehamilan->status != 'hamil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehamilan sudah tidak aktif'
                ], 400);
            }

            // ❌ Sudah punya anak
            $sudahAdaAnak = Anak::where('kehamilan_id', $data['kehamilan_id'])->exists();
            if ($sudahAdaAnak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehamilan ini sudah memiliki anak'
                ], 400);
            }
        }

        // 🔥 AUTO STATUS ANAK
        $tanggalLahir = Carbon::parse($data['tanggal_lahir']);
        $umurBulan = $tanggalLahir->diffInMonths(now());

        if (!isset($data['status'])) {
            if ($umurBulan <= 12) {
                $data['status'] = 'bayi';
            } elseif ($umurBulan <= 59) {
                $data['status'] = 'balita';
            } else {
                $data['status'] = 'anak';
            }
        }

        // 🔥 AUTO ANAK KE
        if (empty($data['anak_ke'])) {
            $jumlahAnak = Anak::where('ibu_id', $data['ibu_id'])->count();
            $data['anak_ke'] = $jumlahAnak + 1;
        }

        // ✅ SIMPAN ANAK
        $anak = Anak::create($data);

        // 🔥 UPDATE STATUS KEHAMILAN JADI SELESAI
        if (!empty($data['kehamilan_id'])) {
            \App\Models\Kehamilan::where('id', $data['kehamilan_id'])
                ->update(['status' => 'selesai']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil disimpan',
            'data' => $anak,
            'usia' => [
                'bulan' => $tanggalLahir->diffInMonths(now()),
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
            'ibu',
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

            'ibu_id' => 'sometimes|required|exists:ibus,id',
            'kehamilan_id' => 'nullable|exists:kehamilans,id',

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

        $anak->update($validator->validated());

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
