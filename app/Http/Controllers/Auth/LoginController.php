<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect setelah login berhasil.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        // Batasi percobaan login: max 5 kali per 1 menit (brute force protection)
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->middleware('throttle:5,1')->only('login');
    }


    /**
     * Override pesan terlalu banyak percobaan login (Bahasa Indonesia).
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.', [
                    'seconds' => $seconds,
                ]),
            ]);
    }
}
