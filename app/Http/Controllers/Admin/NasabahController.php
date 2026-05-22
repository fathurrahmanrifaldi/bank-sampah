<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Nasabah, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB};

class NasabahController extends Controller {

    /** Tampilkan semua nasabah (hanya yang sudah diapprove) */
    public function index() {
        $nasabah = Nasabah::whereHas('user', function($q) {
            $q->where('status', 'approved');
        })->with('user')->latest()->paginate(10);
        return view('admin.nasabah.index', compact('nasabah'));
    }

    /** Tampilkan daftar nasabah pending */
    public function pendingList() {
        $nasabah = Nasabah::whereHas('user', function($q) {
            $q->where('status', 'pending');
        })->with('user')->latest()->paginate(10);
        return view('admin.nasabah.pending', compact('nasabah'));
    }

    /** Setujui nasabah */
    public function approve($id) {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->user->update(['status' => 'approved']);
        return back()->with('success', 'Nasabah berhasil disetujui!');
    }

    /** Tolak nasabah (hapus data) */
    public function reject($id) {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->user->delete(); // cascade ke nasabah otomatis
        return back()->with('success', 'Pendaftaran nasabah ditolak dan data dihapus!');
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
            'nik'     => 'required|string|size:16|unique:nasabah,nik',
            'alamat'  => 'required|string',
            'no_hp'   => 'required|string|max:15',
            'password'=> 'required|min:8',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat user account
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'nasabah',
                'status'   => 'approved', // Jika Admin yang buat, langsung approved
            ]);

            // 2. Buat data nasabah yang terhubung ke user
            Nasabah::create([
                'user_id'     => $user->id,
                'nik'         => $request->nik,
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
            'nik'    => 'required|string|size:16|unique:nasabah,nik,'.$nasabah->id,
            'alamat' => 'required|string',
            'no_hp'  => 'required|string|max:15',
        ]);

        $nasabah->user->update(['name' => $request->name]);
        $nasabah->update([
            'nik'    => $request->nik,
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