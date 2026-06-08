<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelolaAdminController extends Controller
{
    /** Daftar semua akun petugas lapangan (role = admin) */
    public function index()
    {
        $admins = User::where('role', 'admin')->latest()->get();
        return view('super-admin.kelola-admin.index', compact('admins'));
    }

    /** Form buat akun admin baru */
    public function create()
    {
        return view('super-admin.kelola-admin.create');
    }

    /** Simpan akun admin baru */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'email_verified_at' => now(), // Langsung terverifikasi — dibuat oleh Super Admin
        ]);

        return redirect()->route('super-admin.kelola-admin.index')
            ->with('success', "Akun petugas lapangan '{$request->name}' berhasil dibuat!");
    }

    /** Form edit data admin */
    public function edit(User $kelolaAdmin)
    {
        abort_unless($kelolaAdmin->role === 'admin', 404);
        return view('super-admin.kelola-admin.edit', ['admin' => $kelolaAdmin]);
    }

    /** Update data admin */
    public function update(Request $request, User $kelolaAdmin)
    {
        abort_unless($kelolaAdmin->role === 'admin', 404);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $kelolaAdmin->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $kelolaAdmin->update($data);

        return redirect()->route('super-admin.kelola-admin.index')
            ->with('success', "Data petugas '{$kelolaAdmin->name}' berhasil diperbarui!");
    }

    /** Hapus akun admin */
    public function destroy(User $kelolaAdmin)
    {
        abort_unless($kelolaAdmin->role === 'admin', 404);
        $nama = $kelolaAdmin->name;
        $kelolaAdmin->delete();

        return redirect()->route('super-admin.kelola-admin.index')
            ->with('success', "Akun '{$nama}' berhasil dihapus.");
    }

    /** Toggle status aktif / nonaktif */
    public function toggleStatus(User $kelolaAdmin)
    {
        abort_unless($kelolaAdmin->role === 'admin', 404);

        $newStatus = ($kelolaAdmin->status === 'nonaktif') ? 'aktif' : 'nonaktif';
        $kelolaAdmin->update(['status' => $newStatus]);

        $pesan = $newStatus === 'nonaktif'
            ? "Akun '{$kelolaAdmin->name}' telah dinonaktifkan."
            : "Akun '{$kelolaAdmin->name}' telah diaktifkan kembali.";

        return back()->with('success', $pesan);
    }
}
