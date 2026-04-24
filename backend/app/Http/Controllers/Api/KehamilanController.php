<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kehamilan;
use App\Models\Ibu;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KehamilanController extends Controller
{
    /**
     * LIST KEHAMILAN
     */
    public function index(Request $request)
    {
        $query = Kehamilan::with(['ibu'])->latest();

        // 🔍 SEARCH (nama ibu)
        if ($request->search) {
            $search = $request->search;

            $query->whereHas('ibu', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // 🔍 FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // 📍 FILTER IBU
        if ($request->ibu_id) {
            $query->where('ibu_id', $request->ibu_id);
        }

        $data = $query->paginate(10)->withQueryString();

        // ✅ 🔥 INI YANG BENAR
        $data->getCollection()->transform(function ($item) {
            return $this->formatKehamilan($item);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * SIMPAN KEHAMILAN
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ibu_id' => 'required|exists:ibus,id',
            'hpht' => 'required|date',

            // ❌ JANGAN IZINKAN USER SET STATUS
            // 'status' => ...
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 🔥 CEK KEHAMILAN AKTIF
        $existing = Kehamilan::where('ibu_id', $request->ibu_id)
            ->where('status', 'hamil')
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada kehamilan aktif'
            ], 400);
        }

        $data = $validator->validated();
        $data['status'] = 'hamil'; // 🔥 FORCE

        $kehamilan = Kehamilan::create($data);

        // 🔥 SYNC STATUS IBU
        $this->updateStatusIbu($request->ibu_id);

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan berhasil disimpan',
            'data' => $this->formatKehamilan($kehamilan)
        ], 201);
    }

    /**
     * DETAIL KEHAMILAN
     */
    public function show($id)
    {
        $kehamilan = Kehamilan::with(['ibu', 'anak'])->find($id);

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
        $kehamilan = Kehamilan::with('anak')->find($id);

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'hpht' => 'nullable|date',
            'status' => 'nullable|in:hamil,selesai,gugur'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        /// 🔥 VALIDASI HPHT
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

        /// 🔥 VALIDASI STATUS
        if (isset($data['status'])) {

            // ❌ tidak boleh kembali ke hamil jika sebelumnya bukan hamil
            if (
                $data['status'] === 'hamil' &&
                $kehamilan->status !== 'hamil'
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak boleh mengubah kembali ke status hamil'
                ], 400);
            }

            // ❌ tidak boleh ada 2 kehamilan aktif
            if ($data['status'] === 'hamil') {
                $exists = Kehamilan::where('ibu_id', $kehamilan->ibu_id)
                    ->where('status', 'hamil')
                    ->where('id', '!=', $kehamilan->id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Masih ada kehamilan aktif lain'
                    ], 400);
                }
            }

            // ❌ tidak boleh gugur jika sudah punya anak
            if ($data['status'] === 'gugur' && $kehamilan->anak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa gugur karena sudah memiliki anak'
                ], 400);
            }
        }

        /// ✅ UPDATE
        $kehamilan->update($data);

        /// 🔥 AUTO FIX STATUS
        if ($kehamilan->anak) {
            $kehamilan->update(['status' => 'selesai']);
        }

        /// 🔄 SYNC STATUS IBU
        $this->updateStatusIbu($kehamilan->ibu_id);

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan berhasil diupdate',
            'data' => $this->formatKehamilan($kehamilan->fresh())
        ]);
    }

    /**
     * HAPUS
     */
    public function destroy($id)
    {
        $kehamilan = Kehamilan::find($id);

        if (!$kehamilan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $ibuId = $kehamilan->ibu_id;

        $kehamilan->delete();

        // 🔥 SYNC STATUS IBU
        $this->updateStatusIbu($ibuId);

        return response()->json([
            'success' => true,
            'message' => 'Data kehamilan berhasil dihapus'
        ]);
    }

    /**
     * 🔥 FORMAT + HITUNG USIA KEHAMILAN
     */
    private function formatKehamilan($k)
    {
        return [
            'id' => $k->id,
            'hpht' => $k->hpht,
            'status' => $k->status,

            // 🔥 SAMAKAN DENGAN FLUTTER
            'usia_minggu' => (int) $k->usia_kehamilan_minggu,
            'trimester' => $k->trimester,
            'hpl' => $k->hpl,

            'created_at' => $k->created_at,

            'ibu' => [
                'id' => $k->ibu->id, // 🔥 INI WAJIB
                'nama' => $k->ibu->nama ?? '-',
                'no_hp' => $k->ibu->no_hp ?? '-',
                'alamat' => $k->ibu->alamat ?? '-',
            ]
        ];
    }

    private function updateStatusIbu($ibuId)
    {
        $ibu = Ibu::with(['anak', 'kehamilan'])->find($ibuId);

        if (!$ibu) return;

        // 🔥 CEK HAMIL
        if ($ibu->kehamilan()->where('status', 'hamil')->exists()) {
            $ibu->status = 'hamil';
        }
        // 🔥 CEK MENYUSUI
        else {
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
