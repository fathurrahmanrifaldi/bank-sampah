<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Nasabah, DetailTransaksi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller {

    public function index(Request $request) {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Rekapitulasi per nasabah untuk bulan/tahun terpilih
        $rekap = DB::table('transaksi')
            ->join('nasabah', 'transaksi.nasabah_id', '=', 'nasabah.id')
            ->join('users', 'nasabah.user_id', '=', 'users.id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->groupBy('nasabah.id', 'users.name', 'nasabah.nik')
            ->select(
                'users.name',
                'nasabah.nik',
                DB::raw('COUNT(transaksi.id) as jumlah_setor'),
                DB::raw('SUM(transaksi.total_nilai) as total_nilai')
            )->get();

        // Data grafik: total nilai per bulan (12 bulan terakhir)
        $grafik = DB::table('transaksi')
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('SUM(total_nilai) as total')
            )->get();

        // Data grafik kategori: total berat per jenis sampah
        $grafikKategori = DB::table('detail_transaksi')
            ->join('kategori_sampah', 'detail_transaksi.kategori_id', '=', 'kategori_sampah.id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->groupBy('kategori_sampah.nama_kategori')
            ->select(
                'kategori_sampah.nama_kategori',
                DB::raw('SUM(berat_kg) as total_berat')
            )->get();

        return view('admin.laporan.index',
            compact('rekap', 'grafik', 'grafikKategori', 'bulan', 'tahun'));
    }
}