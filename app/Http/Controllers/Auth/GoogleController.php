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
            // Jika user sudah ada, update google_id jika masih kosong
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            // Jika role nasabah, tapi nasabah profile tidak ada (misalnya proses terputus sebelumnya)
            if ($user->role === 'nasabah' && !$user->nasabah) {
                // Simpan data ke session untuk form lengkapi profil
                session()->put('google_registration', [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);
                return redirect()->route('nasabah.complete-profile');
            }

            // Jika status masih pending, tolak login
            if ($user->status === 'pending') {
                return redirect()->route('login')->with('error', 'Akun Anda sedang menunggu persetujuan Admin.');
            }
            if ($user->status === 'rejected') {
                return redirect()->route('login')->with('error', 'Pendaftaran akun Anda ditolak.');
            }

            // Login user
            Auth::login($user);
            return redirect()->route('home'); // redirectAfterLogin akan menghandle redirect dashboard
        }

        // Jika user belum terdaftar sama sekali
        session()->put('google_registration', [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
        ]);

        return redirect()->route('nasabah.complete-profile');
    }
}
