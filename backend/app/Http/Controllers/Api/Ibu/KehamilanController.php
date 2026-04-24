<?php

namespace App\Http\Controllers\Api\Ibu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kehamilan;
use App\Models\Ibu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KehamilanController extends Controller
{
    /**
     * LIST KEHAMILAN MILIK IBU
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

        $data = Kehamilan::with('ibu') // 🔥 eager load
            ->where('ibu_id', $ibu->id)
            ->latest()
            ->paginate(10);

        $data->getCollection()->transform(function ($item) {
            return $this->formatKehamilan($item);
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * SIMPAN KEHAMILAN
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $ibu = Ibu::with('anak', 'kehamilan')->where('user_id', $userId)->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'hpht' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hpht = Carbon::parse($request->hpht);

        // 🔥 1. tidak boleh masa depan
        if ($hpht->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => 'HPHT tidak boleh di masa depan'
            ], 422);
        }

        // 🔥 2. tidak boleh ada kehamilan aktif
        if ($ibu->kehamilan()->where('status', 'hamil')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada kehamilan aktif'
            ], 400);
        }

        // 🔥 3. cek anak terakhir
        $anakTerakhir = $ibu->anak->sortByDesc('tanggal_lahir')->first();

        if ($anakTerakhir) {
            $tglLahir = Carbon::parse($anakTerakhir->tanggal_lahir);

            $jarak = $tglLahir->diffInMonths($hpht);

            // minimal 24 bulan
            if ($jarak < 24) {
                return response()->json([
                    'success' => false,
                    'message' => "Jarak kehamilan minimal 24 bulan (sekarang {$jarak} bulan)"
                ], 400);
            }

            // optional: proteksi postpartum
            if ($tglLahir->diffInMonths(now()) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih dalam masa nifas (minimal 6 bulan)'
                ], 400);
            }
        }

        $kehamilan = Kehamilan::create([
            'ibu_id' => $ibu->id,
            'hpht' => $hpht,
            'status' => 'hamil'
        ]);

        $this->updateStatusIbu($ibu->id);

        return response()->json([
            'success' => true,
            'message' => 'Kehamilan berhasil ditambahkan',
            'data' => $this->formatKehamilan($kehamilan)
        ], 201);
    }

    /**
     * DETAIL
     */
    public function show($id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        if (!$ibu) {
            return response()->json([
                'success' => false,
                'message' => 'Data ibu tidak ditemukan'
            ], 404);
        }

        $kehamilan = Kehamilan::with('ibu')
            ->where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatKehamilan($kehamilan)
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        $kehamilan = Kehamilan::where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        // ✅ VALIDASI (SUDAH SUPPORT hamil)
        $validator = Validator::make($request->all(), [
            'hpht' => 'nullable|date',
            'status' => 'nullable|in:hamil,selesai,gugur'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 RULE PENTING

        // 1. Tidak boleh ubah ke "hamil" jika sebelumnya bukan hamil
        if (
            isset($data['status']) &&
            $data['status'] === 'hamil' &&
            $kehamilan->status !== 'hamil'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh mengubah status kembali ke hamil'
            ], 403);
        }

        // 2. Tidak boleh lebih dari 1 kehamilan aktif
        if (
            isset($data['status']) &&
            $data['status'] === 'hamil'
        ) {
            $cek = Kehamilan::where('ibu_id', $ibu->id)
                ->where('status', 'hamil')
                ->where('id', '!=', $kehamilan->id)
                ->exists();

            if ($cek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada kehamilan aktif lainnya'
                ], 400);
            }
        }

        // 3. Validasi HPHT tidak boleh masa depan
        if (isset($data['hpht'])) {
            $hpht = Carbon::parse($data['hpht']);

            if ($hpht->isFuture()) {
                return response()->json([
                    'success' => false,
                    'message' => 'HPHT tidak boleh di masa depan'
                ], 422);
            }

            $data['hpht'] = $hpht;
        }

        // ✅ UPDATE
        $kehamilan->update($data);

        // 🔄 update status ibu
        $this->updateStatusIbu($ibu->id);

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan berhasil diupdate',
            'data' => $this->formatKehamilan($kehamilan)
        ]);
    }

    /**
     * HAPUS (OPSIONAL)
     */
    public function destroy($id)
    {
        $ibu = Ibu::where('user_id', Auth::id())->first();

        $kehamilan = Kehamilan::where('id', $id)
            ->where('ibu_id', $ibu->id)
            ->first();

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $kehamilan->delete();

        $this->updateStatusIbu($ibu->id);

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan dihapus'
        ]);
    }

    /**
     * FORMAT DATA
     */
    private function formatKehamilan($k)
    {
        return [
            'id' => $k->id,
            'hpht' => $k->hpht,
            'status' => $k->status,
            'usia_minggu' => (int) $k->usia_kehamilan_minggu, // 🔥 fix double
            'trimester' => $k->trimester,
            'hpl' => $k->hpl,
            'created_at' => $k->created_at,

            'ibu' => [
                'nama' => $k->ibu->nama ?? '-',
                'no_hp' => $k->ibu->no_hp ?? '-',
                'alamat' => $k->ibu->alamat ?? '-',
            ]
        ];
    }

    /**
     * UPDATE STATUS IBU
     */
    private function updateStatusIbu($ibuId)
    {
        $ibu = Ibu::with(['anak', 'kehamilan'])->find($ibuId);

        if (!$ibu) return;

        if ($ibu->kehamilan()->where('status', 'hamil')->exists()) {
            $ibu->status = 'hamil';
        } else {
            $menyusui = false;

            foreach ($ibu->anak as $anak) {
                $umur = Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());
                if ($umur <= 24) {
                    $menyusui = true;
                    break;
                }
            }

            $ibu->status = $menyusui ? 'menyusui' : 'calon_ibu';
        }

        $ibu->save();
    }
}
