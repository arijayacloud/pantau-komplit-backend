<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Ibu;

class AuthController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari user manual
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // BUAT TOKEN
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * GET USER LOGIN
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('ibu');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * REGISTER IBU
     */
    public function registerIbu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',

            'nik' => 'required',
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_hp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. USER
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'ibu'
            ]);

            // 2. DATA IBU
            Ibu::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'status' => 'calon_ibu',
                'no_hp' => $request->no_hp,
                'created_by' => $user->id
            ]);

            DB::commit();

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Register ibu gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * REGISTER KADER
     */
    public function registerKader(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'kader'
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Register kader gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE PROFIL IBU
     */
    public function updateProfilIbu(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'ibu') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',

            'nik' => 'sometimes',
            'nama' => 'sometimes',
            'tanggal_lahir' => 'sometimes|date',
            'alamat' => 'sometimes',
            'no_hp' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 🔹 Update USER
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password
                    ? Hash::make($request->password)
                    : $user->password,
            ]);

            // 🔹 Update IBU
            $ibu = Ibu::where('user_id', $user->id)->first();

            if ($ibu) {
                $ibu->update([
                    'nik' => $request->nik ?? $ibu->nik,
                    'nama' => $request->nama ?? $ibu->nama,
                    'tanggal_lahir' => $request->tanggal_lahir ?? $ibu->tanggal_lahir,
                    'alamat' => $request->alamat ?? $ibu->alamat,
                    'no_hp' => $request->no_hp ?? $ibu->no_hp,
                    'updated_by' => $user->id
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil ibu berhasil diupdate',
                'data' => $user->load('ibu') // kalau ada relasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal update profil ibu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE PROFIL KADER
     */
    public function updateProfilKader(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'kader') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password
                    ? Hash::make($request->password)
                    : $user->password,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil kader berhasil diupdate',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update profil kader',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
