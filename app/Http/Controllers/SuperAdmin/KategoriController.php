<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriSampah::latest()->get();
        return view('super-admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('super-admin.kategori.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'jenis'         => 'required|in:organik,anorganik,minyak_bekas,tidak_dapat_diolah',
            'harga_per_kg'  => 'required|numeric|min:0',
            'keterangan'    => 'nullable|string',
        ]);

        KategoriSampah::create($request->only(['nama_kategori', 'jenis', 'harga_per_kg', 'keterangan']));

        return redirect()->route('super-admin.kategori.index')
            ->with('success', 'Kategori sampah berhasil ditambahkan!');
    }

    public function edit(KategoriSampah $kategori)
    {
        return view('super-admin.kategori.form', compact('kategori'));
    }

    public function update(Request $request, KategoriSampah $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'jenis'         => 'required|in:organik,anorganik,minyak_bekas,tidak_dapat_diolah',
            'harga_per_kg'  => 'required|numeric|min:0',
            'keterangan'    => 'nullable|string',
        ]);

        $kategori->update($request->only(['nama_kategori', 'jenis', 'harga_per_kg', 'keterangan']));

        return redirect()->route('super-admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(KategoriSampah $kategori)
    {
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
