<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        // Proteksi: maks 3 request forgot password per 10 menit per IP
        // Mencegah spam / enumerasi email
        $this->middleware('throttle:3,10');
    }

    /**
     * Override: respons ketika email tidak ditemukan — pakai pesan yang tidak
     * mengungkapkan apakah email terdaftar atau tidak (anti-enumeration).
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        // Pakai pesan yang sama baik email ada maupun tidak ada
        // untuk mencegah user/attacker mengetahui email mana yang terdaftar
        return back()
            ->withInput($request->only('email'))
            ->with('status', 'Jika email terdaftar, link reset password akan dikirim ke inbox Anda.');
    }

    /**
     * Override: respons ketika email berhasil dikirim.
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', 'Jika email terdaftar, link reset password akan dikirim ke inbox Anda.');
    }
}
