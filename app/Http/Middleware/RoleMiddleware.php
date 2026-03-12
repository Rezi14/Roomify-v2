<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Mendukung Web (redirect) dan API (JSON response).
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Mengecek apakah user login
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Mengecek apakah role user sesuai dengan parameter route
        // Menggunakan fungsi hasRole() yang sudah ada di model User
        if (!$request->user()->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki akses.'], 403);
            }
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
