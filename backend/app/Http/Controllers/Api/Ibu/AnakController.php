<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anak;
use App\Models\Kehamilan;
use App\Models\Ibu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnakController extends Controller
{
    /**
     * LIST ANAK MILIK IBU
     */
    public function index()
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $data = Anak::with(['kehamilan'])
            ->where('ibu_id', $ibu->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * SIMPAN ANAK
     */
    public function store(Request $request)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'kehamilan_id' => 'nullable|exists:kehamilans,id',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['ibu_id'] = $ibu->id;

        // 🔥 VALIDASI KEHAMILAN
        if (!empty($data['kehamilan_id'])) {
            $kehamilan = Kehamilan::where('id', $data['kehamilan_id'])
                ->where('ibu_id', $ibu->id)
                ->first();

            if (!$kehamilan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehamilan tidak valid'
                ], 403);
            }

            if ($kehamilan->status !== 'hamil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kehamilan tidak aktif'
                ], 400);
            }
        }

        // 🔥 VALIDASI JARAK KELAHIRAN
        $lastAnak = Anak::where('ibu_id', $ibu->id)
            ->orderByDesc('tanggal_lahir')
            ->first();

        $tanggalBaru = Carbon::parse($data['tanggal_lahir']);

        if ($lastAnak) {
            $tanggalTerakhir = Carbon::parse($lastAnak->tanggal_lahir);

            // Anak terakhir masih < 30 hari
            if ($tanggalTerakhir->diffInDays(now()) < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak terakhir masih berusia kurang dari 1 bulan.'
                ], 400);
            }

            // Jarak kelahiran terlalu dekat
            if ($tanggalTerakhir->diffInDays($tanggalBaru) < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jarak kelahiran anak terlalu dekat.'
                ], 400);
            }
        }

        // 🔥 AUTO STATUS
        $umur = Carbon::parse($data['tanggal_lahir'])->diffInMonths(now());

        $data['status'] = $umur <= 12
            ? 'bayi'
            : ($umur <= 59 ? 'balita' : 'anak');

        // 🔥 AUTO ANAK KE
        $jumlah = Anak::where('ibu_id', $ibu->id)->count();
        $data['anak_ke'] = $jumlah + 1;

        $anak = Anak::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Anak berhasil ditambahkan',
            'data' => $anak
        ], 201);
    }

    /**
     * DETAIL ANAK (HANYA MILIK IBU)
     */
    public function show($id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        $anak = Anak::where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->with(['asi', 'pmba.detail'])
            ->first();

        if (!$anak) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $anak
        ]);
    }

    /**
     * UPDATE ANAK
     */
    public function update(Request $request, $id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $anak = Anak::where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$anak) {
            return response()->json([
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'tanggal_lahir' => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 VALIDASI JARAK KELAHIRAN SAAT UPDATE
        if (isset($data['tanggal_lahir'])) {
            $tanggalBaru = Carbon::parse($data['tanggal_lahir']);

            $anakLain = Anak::where('ibu_id', $ibu->id)
                ->where('id', '!=', $anak->id)
                ->orderByDesc('tanggal_lahir')
                ->first();

            if ($anakLain) {
                $tanggalLain = Carbon::parse($anakLain->tanggal_lahir);

                if ($tanggalLain->diffInDays($tanggalBaru) < 30) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Perubahan tanggal lahir melanggar jarak minimal kelahiran.'
                    ], 400);
                }
            }

            // 🔥 UPDATE STATUS OTOMATIS
            $umur = $tanggalBaru->diffInMonths(now());

            $data['status'] = $umur <= 12
                ? 'bayi'
                : ($umur <= 59 ? 'balita' : 'anak');
        }

        $anak->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diupdate',
            'data' => $anak
        ]);
    }

    /**
     * HAPUS (OPSIONAL - biasanya DISABLE)
     */
    public function destroy($id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        $anak = Anak::where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$anak) {
            return response()->json([
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $anak->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data anak dihapus'
        ]);
    }
}
