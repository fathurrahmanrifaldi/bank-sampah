<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login menggunakan Google.');
        }

        // Cek apakah email sudah terdaftar
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Update google_id jika masih kosong
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // Google sudah memverifikasi email — set email_verified_at jika belum ada
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }

            // Jika role nasabah tapi nasabah profile belum ada
            if ($user->role === 'nasabah' && !$user->nasabah) {
                session()->put('google_registration', [
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);
                return redirect()->route('nasabah.complete-profile');
            }

            // Login user
            Auth::login($user);
            return redirect()->route('home');
        }

        // User belum terdaftar — arahkan ke form lengkapi profil
        session()->put('google_registration', [
            'name'      => $googleUser->getName(),
            'email'     => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
        ]);

        return redirect()->route('nasabah.complete-profile');
    }
}
