<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Setelah verifikasi berhasil, langsung ke dashboard nasabah.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('auth');
        // signed: URL verifikasi harus memiliki tanda tangan valid (anti-tamper)
        $this->middleware('signed')->only('verify');
        // throttle resend: maks 1 request per menit via Laravel throttle
        $this->middleware('throttle:3,1')->only('verify', 'resend');
    }

    /**
     * Override resend: tambahkan rate limiting berbasis cache (max 3 email per jam).
     * Mencegah spam kirim email verifikasi.
     */
    public function resend(Request $request)
    {
        // Jika sudah verified, redirect ke home
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $userId = $request->user()->id;
        $cacheKey = "email_verify_resend_{$userId}";

        // Cek berapa kali sudah kirim dalam 1 jam terakhir
        $sentCount = Cache::get($cacheKey, 0);

        if ($sentCount >= 3) {
            return back()->with('resend_error',
                'Batas pengiriman email tercapai (3 kali per jam). Silakan coba lagi nanti atau periksa folder spam.'
            );
        }

        // Kirim email & increment counter
        $request->user()->sendEmailVerificationNotification();
        Cache::put($cacheKey, $sentCount + 1, now()->addHour());

        return back()->with('resent', true);
    }
}
