<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PenjualanPengepul, Transaksi, DetailTransaksi, KategoriSampah};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanPengepulController extends Controller {

    /**
     * Daftar penjualan + stat rekapitulasi uang masuk (all-time, tanpa filter bulan)
     */
    public function index() {
        $penjualan = PenjualanPengepul::with('admin')
            ->latest('tanggal_jual')
            ->paginate(20);

        // Stat keseluruhan (all-time)
        $stat = PenjualanPengepul::selectRaw(
            'COUNT(*) as jumlah_transaksi, SUM(total_uang) as total_uang, AVG(total_uang) as rata_uang'
        )->first();

        // Data grafik 6 bulan terakhir
        // Gunakan startOfMonth() agar tidak terjadi overflow dari tanggal 31
        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $bln = now()->copy()->startOfMonth()->subMonths($i);
            $grafik[] = [
                'label' => $bln->translatedFormat('M Y'),
                'total' => (float) PenjualanPengepul::whereMonth('tanggal_jual', $bln->month)
                    ->whereYear('tanggal_jual', $bln->year)
                    ->sum('total_uang'),
            ];
        }

        return view('admin.penjualan-pengepul.index',
            compact('penjualan', 'stat', 'grafik'));
    }

    /**
     * Form input penjualan – tampilkan ringkasan setoran nasabah pada tanggal terpilih
     */
    public function create(Request $request) {
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Ringkasan setoran nasabah pada tanggal tersebut
        $ringkasan = $this->getRingkasanSetoran($tanggal);

        return view('admin.penjualan-pengepul.create', compact('tanggal', 'ringkasan'));
    }

    /**
     * Simpan data penjualan ke pengepul
     */
    public function store(Request $request) {
        $request->validate([
            'tanggal_jual' => 'required|date',
            'total_uang'   => 'required|numeric|min:1',
            'catatan'      => 'nullable|string|max:500',
        ]);

        PenjualanPengepul::create([
            'tanggal_jual' => $request->tanggal_jual,
            'total_uang'   => $request->total_uang,
            'catatan'      => $request->catatan,
            'admin_id'     => auth()->id(),
        ]);

        return redirect()->route('admin.penjualan-pengepul.index')
            ->with('success', 'Penjualan ke pengepul berhasil dicatat!');
    }

    /**
     * Detail satu record penjualan
     */
    public function show(PenjualanPengepul $penjualanPengepul) {
        $penjualanPengepul->load('admin');
        $ringkasan = $this->getRingkasanSetoran($penjualanPengepul->tanggal_jual->toDateString());

        return view('admin.penjualan-pengepul.show',
            compact('penjualanPengepul', 'ringkasan'));
    }

    /**
     * Form edit penjualan
     */
    public function edit(Request $request, PenjualanPengepul $penjualanPengepul) {
        $penjualanPengepul->load('admin');

        // Jika admin mengubah tanggal di form (untuk reload ringkasan), gunakan tanggal_override
        $tanggal   = $request->get('tanggal_override', $penjualanPengepul->tanggal_jual->toDateString());
        $ringkasan = $this->getRingkasanSetoran($tanggal);

        return view('admin.penjualan-pengepul.edit',
            compact('penjualanPengepul', 'tanggal', 'ringkasan'));
    }

    /**
     * Update data penjualan
     */
    public function update(Request $request, PenjualanPengepul $penjualanPengepul) {
        $request->validate([
            'tanggal_jual' => 'required|date',
            'total_uang'   => 'required|numeric|min:1',
            'catatan'      => 'nullable|string|max:500',
        ]);

        $penjualanPengepul->update([
            'tanggal_jual' => $request->tanggal_jual,
            'total_uang'   => $request->total_uang,
            'catatan'      => $request->catatan,
        ]);

        return redirect()->route('admin.penjualan-pengepul.index')
            ->with('success', 'Data penjualan berhasil diperbarui!');
    }

    /**
     * Hapus data penjualan
     */
    public function destroy(PenjualanPengepul $penjualanPengepul) {
        $penjualanPengepul->delete();

        return redirect()->route('admin.penjualan-pengepul.index')
            ->with('success', 'Data penjualan berhasil dihapus!');
    }

    // ─── Private helper ────────────────────────────────────────────

    /**
     * Ambil ringkasan total setoran nasabah pada tanggal tertentu
     * (sebagai referensi informasional untuk admin)
     */
    private function getRingkasanSetoran(string $tanggal): object {
        $transaksi = Transaksi::whereDate('tanggal', $tanggal)->get();

        $jumlahNasabah = $transaksi->count();
        $nilaiTotal    = $transaksi->sum('total_nilai');

        // Breakdown per kategori
        $kategoriBreakdown = DB::table('detail_transaksi')
            ->join('kategori_sampah', 'detail_transaksi.kategori_id', '=', 'kategori_sampah.id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->whereDate('transaksi.tanggal', $tanggal)
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama_kategori')
            ->select(
                'kategori_sampah.nama_kategori',
                DB::raw('SUM(detail_transaksi.berat_kg) as total_berat'),
                DB::raw('SUM(detail_transaksi.nilai) as total_nilai')
            )->get();

        return (object) [
            'jumlah_nasabah'    => $jumlahNasabah,
            'nilai_total'       => $nilaiTotal,
            'kategori_breakdown' => $kategoriBreakdown,
        ];
    }
}
