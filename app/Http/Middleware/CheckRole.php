<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole {

    public function handle(Request $request, Closure $next, string $role) {
        // Jika belum login, redirect ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Jika role tidak sesuai, tolak akses
        if (auth()->user()->role !== $role) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}