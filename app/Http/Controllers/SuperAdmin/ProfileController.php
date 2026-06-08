<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Tampilkan form edit profil */
    public function edit()
    {
        return view('super-admin.profil.edit', ['user' => auth()->user()]);
    }

    /** Update nama saja */
    public function updateProfil(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        auth()->user()->update(['name' => $request->name]);

        return back()->with('success', 'Nama profil berhasil diperbarui!');
    }

    /** Update password */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required',
        ]);

        // Verifikasi password lama
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Password berhasil diubah!');
    }
}
