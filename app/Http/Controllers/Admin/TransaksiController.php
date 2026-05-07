<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, DetailTransaksi, Nasabah, KategoriSampah};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller {

    public function index() {
        $transaksi = Transaksi::with(['nasabah.user', 'admin'])
            ->latest()->paginate(15);
        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create() {
        $nasabah  = Nasabah::with('user')->get();
        $kategori = KategoriSampah::all();
        return view('admin.transaksi.create', compact('nasabah', 'kategori'));
    }

    /**
     * Simpan transaksi + detail + update saldo
     * Menggunakan DB::transaction untuk keamanan data
     */
    public function store(Request $request) {
        $request->validate([
            'nasabah_id'    => 'required|exists:nasabah,id',
            'tanggal'       => 'required|date',
            'kategori_id'   => 'required|array|min:1',
            'kategori_id.*' => 'exists:kategori_sampah,id',
            'berat_kg.*'    => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request) {
            $totalNilai = 0;
            $details    = [];

            // Hitung nilai per kategori
            foreach ($request->kategori_id as $i => $kategoriId) {
                $kategori   = KategoriSampah::findOrFail($kategoriId);
                $beratKg    = (float) $request->berat_kg[$i];
                $nilai      = $beratKg * $kategori->harga_per_kg;
                $totalNilai += $nilai;

                $details[] = [
                    'kategori_id' => $kategoriId,
                    'berat_kg'    => $beratKg,
                    'nilai'       => $nilai,
                ];
            }

            // Buat header transaksi
            $transaksi = Transaksi::create([
                'nasabah_id'  => $request->nasabah_id,
                'admin_id'    => auth()->id(),
                'tanggal'     => $request->tanggal,
                'total_nilai' => $totalNilai,
                'catatan'     => $request->catatan,
            ]);

            // Simpan detail transaksi
            foreach ($details as $d) {
                $transaksi->detail()->create($d);
            }

            // ✅ Update saldo nasabah secara akumulatif
            Nasabah::find($request->nasabah_id)
                ->increment('saldo', $totalNilai);
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil dicatat dan saldo diperbarui!');
    }

    public function show(Transaksi $transaksi) {
        $transaksi->load(['nasabah.user', 'detail.kategori', 'admin']);
        return view('admin.transaksi.show', compact('transaksi'));
    }
}