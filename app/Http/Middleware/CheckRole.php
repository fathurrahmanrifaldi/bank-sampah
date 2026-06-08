<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole {

    /**
     * Cek role user. Mendukung multi-role: role:admin,super_admin
     * Juga memblokir akun dengan status 'nonaktif'.
     */
    public function handle(Request $request, Closure $next, string ...$roles) {
        // Jika belum login, redirect ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Jika akun dinonaktifkan oleh Super Admin, paksa logout
        if ($user->status === 'nonaktif') {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi Ketua RW untuk informasi lebih lanjut.');
        }

        // Jika role tidak sesuai, tolak akses
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}