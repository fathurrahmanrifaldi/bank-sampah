<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Setelah registrasi, redirect ke halaman "cek email".
     * User sudah login tapi perlu verifikasi email dulu.
     */
    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validasi input registrasi.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nik'      => ['required', 'string', 'size:16', 'unique:nasabah,nik'],
            'no_hp'    => ['nullable', 'string', 'max:15'],
            'alamat'   => ['nullable', 'string'],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email ini sudah terdaftar. Silakan gunakan email lain.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'nik.required'      => 'NIK wajib diisi.',
            'nik.size'          => 'NIK harus tepat 16 digit.',
            'nik.unique'        => 'NIK ini sudah terdaftar.',
        ]);
    }

    /**
     * Override register: buat user, login, redirect ke halaman verifikasi email.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        DB::beginTransaction();
        try {
            $user = $this->create($request->all());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')->withInput();
        }

        // Login user, lalu kirim email verifikasi (MustVerifyEmail otomatis)
        $this->guard()->login($user);
        $user->sendEmailVerificationNotification();

        // Redirect ke halaman "cek email kamu"
        return redirect()->route('verification.notice');
    }

    /**
     * Buat user baru — status langsung approved (tidak perlu persetujuan Admin).
     * Email verification yang menjadi gatekeeper.
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'nasabah',
            'status'   => 'approved', // langsung approved, gatekeeper adalah email verification
        ]);

        $user->nasabah()->create([
            'nik'    => $data['nik'],
            'no_hp'  => $data['no_hp'] ?? null,
            'alamat' => $data['alamat'] ?? null,
        ]);

        return $user;
    }
}
