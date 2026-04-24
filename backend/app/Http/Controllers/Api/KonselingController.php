<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KonselingController extends Controller
{
    /**
     * 📋 LIST KONSELING
     */
    public function index(Request $request)
    {
        $query = Konseling::query();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->resiko) {
            $query->where('resiko', $request->resiko);
        }

        // 🔥 filter kehamilan (minggu)
        if ($request->minggu) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('min_minggu')
                    ->orWhere('min_minggu', '<=', $request->minggu);
            })->where(function ($q) use ($request) {
                $q->whereNull('max_minggu')
                    ->orWhere('max_minggu', '>=', $request->minggu);
            });
        }

        // 🔥 filter anak (bulan)
        if ($request->bulan) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('min_bulan')
                    ->orWhere('min_bulan', '<=', $request->bulan);
            })->where(function ($q) use ($request) {
                $q->whereNull('max_bulan')
                    ->orWhere('max_bulan', '>=', $request->bulan);
            });
        }

        $data = $query
            ->orderByDesc('priority') // 🔥 penting
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * 💾 SIMPAN
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'nullable|string|max:255',
            'materi' => 'required|string',

            'min_minggu' => 'nullable|integer',
            'max_minggu' => 'nullable|integer|gte:min_minggu',

            'min_bulan' => 'nullable|integer',
            'max_bulan' => 'nullable|integer|gte:min_bulan',

            'kategori' => 'required|in:kehamilan,anak',
            'resiko' => 'required|in:normal,tinggi',
            'priority' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $konseling = Konseling::create($validator->validated());
        $validator->after(function ($validator) use ($request) {

            if ($request->kategori === 'kehamilan') {
                if ($request->min_bulan || $request->max_bulan) {
                    $validator->errors()->add('bulan', 'Kategori kehamilan tidak boleh pakai bulan');
                }
            }

            if ($request->kategori === 'anak') {
                if ($request->min_minggu || $request->max_minggu) {
                    $validator->errors()->add('minggu', 'Kategori anak tidak boleh pakai minggu');
                }
            }
        });

        return response()->json([
            'success' => true,
            'data' => $konseling
        ], 201);
    }

    /**
     * 🔍 DETAIL
     */
    public function show($id)
    {
        $konseling = Konseling::find($id);

        if (!$konseling) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $konseling
        ]);
    }

    /**
     * ✏️ UPDATE
     */
    public function update(Request $request, $id)
    {
        $konseling = Konseling::find($id);

        if (!$konseling) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'nullable|string|max:255',
            'materi' => 'nullable|string',

            'min_minggu' => 'nullable|integer',
            'max_minggu' => 'nullable|integer|gte:min_minggu',

            'min_bulan' => 'nullable|integer',
            'max_bulan' => 'nullable|integer|gte:min_bulan',

            'kategori' => 'required|in:kehamilan,anak',
            'resiko' => 'required|in:normal,tinggi',
            'priority' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $konseling->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $konseling
        ]);
    }

    /**
     * 🗑️ DELETE
     */
    public function destroy($id)
    {
        $konseling = Konseling::find($id);

        if (!$konseling) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $konseling->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konseling berhasil dihapus'
        ]);
    }

    /**
     * 🔥 API KHUSUS AUTO KONSELING (dipakai Flutter)
     */
    public function getByUsia(Request $request)
    {
        try {
            $kategori = $request->kategori;
            $resiko = $request->resiko ?? 'normal';

            if (!in_array($kategori, ['kehamilan', 'anak'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak valid'
                ], 400);
            }

            $query = Konseling::where('kategori', $kategori);

            // 🔥 APPLY FILTER USIA
            $query = $this->applyUsiaFilter($query, $kategori, $request);

            $data = $query
                ->whereIn('resiko', [$resiko, 'normal'])
                ->orderByRaw("resiko = ? desc", [$resiko])
                ->orderByDesc('priority')
                ->get();

            // 🔥 FALLBACK JIKA KOSONG
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'total' => 0,
                    'message' => 'Tidak ada konseling yang sesuai',
                    'data' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'total' => $data->count(),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function applyUsiaFilter($query, $kategori, $request)
    {
        if ($kategori === 'kehamilan') {
            if (!$request->minggu) {
                throw new \Exception('Parameter minggu wajib untuk kategori kehamilan');
            }

            $minggu = max(0, (int)$request->minggu);

            $query->where(function ($q) use ($minggu) {
                $q->whereNull('min_minggu')
                    ->orWhere('min_minggu', '<=', $minggu);
            })->where(function ($q) use ($minggu) {
                $q->whereNull('max_minggu')
                    ->orWhere('max_minggu', '>=', $minggu);
            });
        }

        if ($kategori === 'anak') {
            if (!$request->bulan) {
                throw new \Exception('Parameter bulan wajib untuk kategori anak');
            }

            $bulan = max(0, (int)$request->bulan);

            $query->where(function ($q) use ($bulan) {
                $q->whereNull('min_bulan')
                    ->orWhere('min_bulan', '<=', $bulan);
            })->where(function ($q) use ($bulan) {
                $q->whereNull('max_bulan')
                    ->orWhere('max_bulan', '>=', $bulan);
            });
        }

        return $query;
    }
}
