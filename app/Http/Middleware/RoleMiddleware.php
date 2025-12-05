<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    // Handle an incoming request.
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Mngecek apakah user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Mengecek apakah role user sesuai dengan parameter route
        // Menggunakan fungsi hasRole() yang sudah ada di model User
        if (!$request->user()->hasRole($role)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
