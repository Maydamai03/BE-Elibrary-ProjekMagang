<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role  // Ini adalah parameter yang kita kirim dari file rute, contoh: 'admin'
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // 1. Periksa apakah user sudah login DAN
        // 2. Periksa apakah role user yang login SAMA DENGAN role yang dibutuhkan ($role)
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Jika tidak sesuai, tolak akses dengan pesan error 403 (Forbidden)
            return response()->json(['message' => 'Akses ditolak. Anda tidak memiliki hak akses yang sesuai.'], 403);
        }

        // Jika user memiliki role yang sesuai, lanjutkan request ke controller
        return $next($request);
    }
}
