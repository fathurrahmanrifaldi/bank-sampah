<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Nasabah;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /** Form Lengkapi Profil dari Google OAuth */
    public function completeProfileForm()
    {
        if (!session()->has('google_registration')) {
            return redirect()->route('login');
        }
        $googleData = session('google_registration');
        return view('auth.complete_profile', compact('googleData'));
    }

    /** Simpan profil dari Google OAuth */
    public function completeProfileStore(Request $request)
    {
        if (!session()->has('google_registration')) {
            return redirect()->route('login');
        }

        $request->validate([
            'nik' => 'required|string|size:16|unique:nasabah,nik',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $googleData = session('google_registration');

        DB::transaction(function () use ($request, $googleData) {
            // Cek apakah user dengan email ini sudah ada tapi belum punya nasabah
            $user = User::where('email', $googleData['email'])->first();

            if (!$user) {
                // Buat user baru jika belum ada — Google sudah verifikasi email
                $user = User::create([
                    'name'              => $googleData['name'],
                    'email'             => $googleData['email'],
                    'password'          => Hash::make($request->password),
                    'role'              => 'nasabah',
                    'status'            => 'approved',
                    'google_id'         => $googleData['google_id'],
                    'email_verified_at' => now(), // Google sudah verifikasi
                ]);
            } else {
                // Jika sudah ada tapi proses terputus, update data
                $user->update([
                    'password'          => Hash::make($request->password),
                    'google_id'         => $googleData['google_id'],
                    'status'            => 'approved',
                    'email_verified_at' => now(),
                ]);
            }

            // Buat profil nasabah
            Nasabah::create([
                'user_id' => $user->id,
                'nik'     => $request->nik,
                'no_hp'   => $request->no_hp,
                'alamat'  => $request->alamat,
                'saldo'   => 0,
            ]);
        });

        $user = User::where('email', $googleData['email'])->first();
        session()->forget('google_registration');

        // Login langsung dan arahkan ke dashboard
        auth()->login($user);
        return redirect()->route('nasabah.dashboard')
            ->with('success', 'Selamat datang! Akun Anda berhasil dibuat via Google.');
    }

    /** Edit Profile Nasabah (Sesudah Login) */
    public function edit()
    {
        $user = auth()->user();
        $nasabah = $user->nasabah;
        return view('nasabah.profil', compact('user', 'nasabah'));
    }

    /** Update Profile Nasabah */
    public function update(Request $request)
    {
        $user = auth()->user();
        $nasabah = $user->nasabah;

        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update User
        $userData = ['name' => $request->name];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        // Update Nasabah
        $nasabah->update([
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
