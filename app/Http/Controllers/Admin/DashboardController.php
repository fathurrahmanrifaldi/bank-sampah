<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Nasabah, Transaksi, DetailTransaksi};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // ── Stat cards ──────────────────────────────────────────
        $totalNasabah = Nasabah::count();

        $transaksiBuilan = Transaksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)->count();

        $totalBeratBulan = DetailTransaksi::whereHas('transaksi', function ($q) use ($now) {
            $q->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year);
        })->sum('berat_kg');

        $nilaiBuilan = Transaksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)->sum('total_nilai');

        // ── Transaksi terbaru (5 data) ───────────────────────────
        $transaksiTerbaru = Transaksi::with('nasabah.user')
            ->latest()->take(5)->get();

        // ── Data chart: 6 bulan terakhir ────────────────────────
        $chartLabels = [];
        $chartNilai  = [];
        $chartBerat  = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = $now->copy()->subMonths($i);
            $chartLabels[] = $bulan->translatedFormat('M Y');

            $nilaiRow = Transaksi::whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)->sum('total_nilai');
            $chartNilai[] = (float) $nilaiRow;

            $beratRow = DetailTransaksi::whereHas('transaksi', function ($q) use ($bulan) {
                $q->whereMonth('tanggal', $bulan->month)->whereYear('tanggal', $bulan->year);
            })->sum('berat_kg');
            $chartBerat[] = (float) $beratRow;
        }

        return view('admin.dashboard.index', compact(
            'totalNasabah', 'transaksiBuilan', 'totalBeratBulan',
            'nilaiBuilan', 'transaksiTerbaru',
            'chartLabels', 'chartNilai', 'chartBerat'
        ));
    }
}