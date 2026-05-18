<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // 1. cek login
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // 2. normalisasi role dari parameter middleware
        $roles = array_map('strtolower', array_map('trim', $roles));

        // 3. ambil role user + normalisasi
        $userRole = strtolower(trim($user->role->name ?? ''));

        // 4. debug aman (hapus kalau sudah fix)
        // dd($userRole, $roles);

        // 5. check permission
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return $next($request);
    }
}
