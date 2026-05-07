<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Nasabah, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB};

class NasabahController extends Controller {

    /** Tampilkan semua nasabah */
    public function index() {
        $nasabah = Nasabah::with('user')->latest()->paginate(10);
        return view('admin.nasabah.index', compact('nasabah'));
    }

    /** Form tambah nasabah baru */
    public function create() {
        return view('admin.nasabah.create');
    }

    /** Simpan nasabah baru ke database */
    public function store(Request $request) {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email',
            'alamat'  => 'required|string',
            'no_hp'   => 'required|string|max:15',
            'password'=> 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat user account
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'nasabah',
            ]);

            // 2. Buat data nasabah yang terhubung ke user
            Nasabah::create([
                'user_id'     => $user->id,
                'no_rekening' => Nasabah::generateNoRekening(),
                'alamat'      => $request->alamat,
                'no_hp'       => $request->no_hp,
                'saldo'       => 0,
            ]);
        });

        return redirect()->route('admin.nasabah.index')
            ->with('success', 'Nasabah berhasil ditambahkan!');
    }

    /** Form edit nasabah */
    public function edit(Nasabah $nasabah) {
        return view('admin.nasabah.edit', compact('nasabah'));
    }

    /** Update data nasabah */
    public function update(Request $request, Nasabah $nasabah) {
        $request->validate([
            'name'   => 'required|string|max:100',
            'alamat' => 'required|string',
            'no_hp'  => 'required|string|max:15',
        ]);

        $nasabah->user->update(['name' => $request->name]);
        $nasabah->update([
            'alamat' => $request->alamat,
            'no_hp'  => $request->no_hp,
        ]);

        return redirect()->route('admin.nasabah.index')
            ->with('success', 'Data nasabah diperbarui!');
    }

    /** Hapus nasabah */
    public function destroy(Nasabah $nasabah) {
        $nasabah->user->delete(); // cascade ke nasabah otomatis
        return back()->with('success', 'Nasabah berhasil dihapus!');
    }
}