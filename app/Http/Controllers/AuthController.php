<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Setelah login berhasil, redirect ke dashboard sesuai role.
     * Dipanggil dari route /home (HOME_URL default Laravel Auth).
     */
    public function redirectAfterLogin()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('nasabah.dashboard');
    }
}