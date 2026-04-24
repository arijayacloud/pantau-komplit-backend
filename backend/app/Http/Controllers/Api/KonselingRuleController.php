<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KonselingRule;
use Illuminate\Support\Facades\Validator;

class KonselingRuleController extends Controller
{
    /**
     * List rule
     */
    public function index(Request $request)
    {
        $query = KonselingRule::query();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->parameter) {
            $query->where('parameter', $request->parameter);
        }

        $data = $query
            ->orderByDesc('priority')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Simpan rule
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|in:ttd,bumil,anak,asi',
            'parameter' => 'required|string|max:100',
            'operator' => 'required|in:=,!=,>,<,>=,<=',
            'value' => 'required',
            'isi_konseling' => 'required|string',
            'priority' => 'nullable|integer|min:1',
            'score' => 'nullable|integer',
            'is_risk' => 'nullable|boolean'
        ]);

        $validator->after(function ($validator) use ($request) {

            // 🔥 CEK DUPLIKAT RULE
            $exists = KonselingRule::where('kategori', $request->kategori)
                ->where('parameter', $request->parameter)
                ->where('operator', $request->operator)
                ->where('value', $request->value)
                ->exists();

            if ($exists) {
                $validator->errors()->add('rule', 'Rule sudah ada');
            }

            // 🔥 VALIDASI NUMERIC OPERATOR
            if (in_array($request->operator, ['>', '<', '>=', '<=']) && !is_numeric($request->value)) {
                $validator->errors()->add('value', 'Value harus numerik untuk operator ini');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔥 DEFAULT PRIORITY
        $data['priority'] = $data['priority'] ?? 1;

        $rule = KonselingRule::create($data);

        return response()->json([
            'success' => true,
            'data' => $rule
        ], 201);
    }

    /**
     * Detail rule
     */
    public function show($id)
    {
        $data = KonselingRule::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Rule tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update rule
     */
    public function update(Request $request, $id)
    {
        $rule = KonselingRule::find($id);

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'Rule tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori' => 'sometimes|in:ttd,bumil,anak,asi',
            'parameter' => 'sometimes|string|max:100',
            'operator' => 'sometimes|in:=,!=,>,<,>=,<=',
            'value' => 'sometimes',
            'isi_konseling' => 'sometimes|string',
            'priority' => 'sometimes|integer|min:1',
            'score' => 'sometimes|integer',
            'is_risk' => 'sometimes|boolean'
        ]);

        $validator->after(function ($validator) use ($request, $id, $rule) {
            $kategori = $request->kategori ?? $rule->kategori;
            $parameter = $request->parameter ?? $rule->parameter;
            $operator = $request->operator ?? $rule->operator;
            $value = $request->value ?? $rule->value;

            $exists = KonselingRule::where('id', '!=', $id)
                ->where('kategori', $kategori)
                ->where('parameter', $parameter)
                ->where('operator', $operator)
                ->where('value', $value)
                ->exists();

            if ($exists) {
                $validator->errors()->add('rule', 'Rule duplikat');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $rule->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $rule->fresh()
        ]);
    }

    /**
     * Hapus rule
     */
    public function destroy($id)
    {
        $rule = KonselingRule::find($id);

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'Rule tidak ditemukan'
            ], 404);
        }

        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rule berhasil dihapus'
        ]);
    }

    private function evaluate($left, $operator, $right)
    {
        return match ($operator) {
            '='  => $left == $right,
            '!=' => $left != $right,
            '>'  => $left > $right,
            '<'  => $left < $right,
            '>=' => $left >= $right,
            '<=' => $left <= $right,
            default => false
        };
    }

    private function normalizeValue($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));

            if ($lower === 'true') return true;
            if ($lower === 'false') return false;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return trim((string)$value);
    }
}
