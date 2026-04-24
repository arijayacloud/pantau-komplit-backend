<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle request berdasarkan role user
     *
     * contoh penggunaan:
     * ->middleware('role:admin')
     * ->middleware('role:ibu')
     * ->middleware('role:admin,ibu')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // 🔥 CEK LOGIN
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // 🔥 CEK ROLE ADA DI USER
        if (!isset($user->role)) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan'
            ], 403);
        }

        // 🔥 NORMALISASI ROLE (lowercase)
        $userRole = strtolower($user->role);

        $allowedRoles = array_map(fn($r) => strtolower($r), $roles);

        // 🔥 CEK ROLE
        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
                'required_role' => $allowedRoles,
                'your_role' => $userRole
            ], 403);
        }

        return $next($request);
    }
}
