<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RemajaPutri;
use Illuminate\Support\Facades\Validator;

class RemajaController extends Controller
{
    /**
     * LIST DATA
     */
    public function index(Request $request)
    {
        $query = RemajaPutri::query();

        // ======================
        // 🔍 SEARCH
        // ======================
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('sekolah', 'like', "%$search%")
                    ->orWhere('kelas', 'like', "%$search%")
                    ->orWhere('no_hp', 'like', "%$search%");
            });
        }

        // ======================
        // 🎯 FILTER
        // ======================

        // Filter sekolah
        if ($request->filled('sekolah')) {
            $query->where('sekolah', $request->sekolah);
        }

        // Filter sudah menstruasi
        if ($request->filled('sudah_menstruasi')) {
            $query->where('sudah_menstruasi', $request->sudah_menstruasi);
        }

        // Filter HB rendah (contoh < 12)
        if ($request->filled('hb_rendah')) {
            if ($request->hb_rendah == 1) {
                $query->where('hb', '<', 12);
            }
        }

        // ======================
        // SORTING
        // ======================
        $query->latest(); // order by created_at desc

        // ======================
        // 📄 PAGINATION
        // ======================
        $perPage = $request->get('per_page', 10);

        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data remaja berhasil diambil',
            'data' => $data->items(),

            // 🔥 pagination meta
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * SIMPAN DATA
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // IDENTITAS
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',

            // SEKOLAH
            'sekolah' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:50',

            // ALAMAT
            'alamat' => 'nullable|string',

            // KESEHATAN
            'hb' => 'nullable|integer',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',

            // MENSTRUASI
            'sudah_menstruasi' => 'nullable|boolean',
            'tanggal_menstruasi_terakhir' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // default boolean
        $data['sudah_menstruasi'] = $request->input('sudah_menstruasi', false);

        $remaja = RemajaPutri::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data remaja berhasil disimpan',
            'data' => $remaja
        ], 201);
    }

    /**
     * DETAIL
     */
    public function show($id)
    {
        $remaja = RemajaPutri::find($id);

        if (!$remaja) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $remaja
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $remaja = RemajaPutri::find($id);

        if (!$remaja) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'sekolah' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'hb' => 'nullable|integer',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'sudah_menstruasi' => 'nullable|boolean',
            'tanggal_menstruasi_terakhir' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // handle boolean (biar gak null)
        if ($request->has('sudah_menstruasi')) {
            $data['sudah_menstruasi'] = $request->input('sudah_menstruasi');
        }

        $remaja->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data remaja berhasil diupdate',
            'data' => $remaja
        ]);
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $remaja = RemajaPutri::find($id);

        if (!$remaja) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $remaja->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data remaja berhasil dihapus'
        ]);
    }
}
