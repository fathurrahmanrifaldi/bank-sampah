<?php
namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, DetailTransaksi, Penilaian};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $nasabah = auth()->user()->nasabah;
        $now     = Carbon::now();

        $totalTransaksi = $nasabah->transaksi()->count();

        $totalBerat = DetailTransaksi::whereHas('transaksi', function ($q) use ($nasabah) {
            $q->where('nasabah_id', $nasabah->id);
        })->sum('berat_kg');

        // Predikat bulan ini
        $predikat = Penilaian::where('nasabah_id', $nasabah->id)
            ->where('bulan', $now->month)
            ->where('tahun', $now->year)
            ->value('predikat');

        // 5 transaksi terbaru beserta detail
        $transaksiTerbaru = $nasabah->transaksi()
            ->with('detail.kategori')
            ->latest('tanggal')->take(5)->get();

        return view('nasabah.dashboard.index',
            compact('nasabah', 'totalTransaksi', 'totalBerat', 'predikat', 'transaksiTerbaru'));
    }
}