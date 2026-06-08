<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Nasabah, DetailTransaksi, PenarikanDana, PenjualanPengepul};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller {

    public function index(Request $request) {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // ─── 1. Rekap Setoran Nasabah ──────────────────────────────────────────
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

        // ─── 2. Grafik total nilai setoran per bulan ───────────────────────────
        $grafik = DB::table('transaksi')
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('SUM(total_nilai) as total')
            )->get();

        // ─── 3. Grafik komposisi sampah per kategori ───────────────────────────
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

        // ─── 4. Rekap Penarikan Dana ───────────────────────────────────────────
        $rekapPenarikan = DB::table('penarikan_dana')
            ->join('nasabah', 'penarikan_dana.nasabah_id', '=', 'nasabah.id')
            ->join('users', 'nasabah.user_id', '=', 'users.id')
            ->whereMonth('penarikan_dana.tanggal_diminta', $bulan)
            ->whereYear('penarikan_dana.tanggal_diminta', $tahun)
            ->select(
                'users.name',
                'nasabah.nik',
                'penarikan_dana.jumlah',
                'penarikan_dana.jenis',
                'penarikan_dana.status',
                'penarikan_dana.tanggal_diminta',
                'penarikan_dana.tanggal_pencairan'
            )->orderByDesc('penarikan_dana.tanggal_diminta')->get();

        // Stat penarikan bulan ini
        $statPenarikan = (object)[
            'total_disetujui'  => DB::table('penarikan_dana')
                ->whereMonth('tanggal_diminta', $bulan)->whereYear('tanggal_diminta', $tahun)
                ->where('status', 'disetujui')->sum('jumlah'),
            'total_menunggu'   => DB::table('penarikan_dana')
                ->whereMonth('tanggal_diminta', $bulan)->whereYear('tanggal_diminta', $tahun)
                ->where('status', 'menunggu')->sum('jumlah'),
            'total_ditolak'    => DB::table('penarikan_dana')
                ->whereMonth('tanggal_diminta', $bulan)->whereYear('tanggal_diminta', $tahun)
                ->where('status', 'ditolak')->sum('jumlah'),
            'jumlah_pengajuan' => DB::table('penarikan_dana')
                ->whereMonth('tanggal_diminta', $bulan)->whereYear('tanggal_diminta', $tahun)
                ->count(),
        ];

        // Grafik penarikan per bulan (tahun ini)
        $grafikPenarikan = DB::table('penarikan_dana')
            ->whereYear('tanggal_diminta', $tahun)
            ->where('status', 'disetujui')
            ->groupBy(DB::raw('MONTH(tanggal_diminta)'))
            ->select(
                DB::raw('MONTH(tanggal_diminta) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )->get();

        // ─── 5. Rekap Penjualan Pengepul ──────────────────────────────────────
        $rekapPengepul = DB::table('penjualan_pengepul')
            ->join('users', 'penjualan_pengepul.admin_id', '=', 'users.id')
            ->whereMonth('penjualan_pengepul.tanggal_jual', $bulan)
            ->whereYear('penjualan_pengepul.tanggal_jual', $tahun)
            ->select(
                'penjualan_pengepul.tanggal_jual',
                'users.name as admin_name',
                'penjualan_pengepul.total_uang',
                'penjualan_pengepul.catatan'
            )->orderByDesc('penjualan_pengepul.tanggal_jual')->get();

        $statPengepul = (object)[
            'total_bulan_ini'  => DB::table('penjualan_pengepul')
                ->whereMonth('tanggal_jual', $bulan)->whereYear('tanggal_jual', $tahun)
                ->sum('total_uang'),
            'jumlah_transaksi' => DB::table('penjualan_pengepul')
                ->whereMonth('tanggal_jual', $bulan)->whereYear('tanggal_jual', $tahun)
                ->count(),
            'total_tahun_ini'  => DB::table('penjualan_pengepul')
                ->whereYear('tanggal_jual', $tahun)->sum('total_uang'),
        ];

        // Grafik penjualan pengepul per bulan (tahun ini)
        $grafikPengepul = DB::table('penjualan_pengepul')
            ->whereYear('tanggal_jual', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal_jual)'))
            ->select(
                DB::raw('MONTH(tanggal_jual) as bulan'),
                DB::raw('SUM(total_uang) as total')
            )->get();

        // ─── 6. Ringkasan Keuangan Bulanan ─────────────────────────────────────
        $totalSetoran   = $rekap->sum('total_nilai');
        $totalDicairkan = $statPenarikan->total_disetujui;
        $totalPengepul  = $statPengepul->total_bulan_ini;

        // ─── 7. Aktivitas per Admin (khusus Super Admin) ───────────────────────
        $aktivitasAdmin = DB::table('transaksi')
            ->join('users', 'transaksi.admin_id', '=', 'users.id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->groupBy('users.id', 'users.name')
            ->select(
                'users.name as admin_name',
                DB::raw('COUNT(transaksi.id) as jumlah_transaksi'),
                DB::raw('SUM(transaksi.total_nilai) as total_nilai')
            )->get();

        return view('super-admin.laporan.index', compact(
            'rekap', 'grafik', 'grafikKategori',
            'rekapPenarikan', 'statPenarikan', 'grafikPenarikan',
            'rekapPengepul', 'statPengepul', 'grafikPengepul',
            'totalSetoran', 'totalDicairkan', 'totalPengepul',
            'aktivitasAdmin',
            'bulan', 'tahun'
        ));
    }
}
