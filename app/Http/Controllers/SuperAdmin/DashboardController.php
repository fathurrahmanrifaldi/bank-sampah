<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Nasabah, Transaksi, DetailTransaksi, User, PenarikanDana};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // ── Stat cards ───────────────────────────────────────────────────────
        $totalNasabah     = Nasabah::count();
        $totalAdminAktif  = User::where('role', 'admin')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'nonaktif');
            })->count();
        $totalTransaksi   = Transaksi::count();
        $totalNilaiSetoran = Transaksi::sum('total_nilai');

        // Setoran bulan ini
        $setoranBulanIni = Transaksi::whereMonth('tanggal', $now->month)
            ->whereYear('tanggal', $now->year)
            ->sum('total_nilai');

        // Penarikan menunggu
        $penarikanMenunggu = PenarikanDana::where('status', 'menunggu')->count();

        // ── Chart 6 bulan terakhir ────────────────────────────────────────────
        $chartLabels = [];
        $chartNilai  = [];
        $chartBerat  = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = $now->copy()->subMonths($i);
            $chartLabels[] = $bulan->translatedFormat('M Y');

            $chartNilai[] = (float) Transaksi::whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->sum('total_nilai');

            $chartBerat[] = (float) DetailTransaksi::whereHas('transaksi', function ($q) use ($bulan) {
                $q->whereMonth('tanggal', $bulan->month)
                  ->whereYear('tanggal', $bulan->year);
            })->sum('berat_kg');
        }

        // ── Daftar admin aktif ────────────────────────────────────────────────
        $admins = User::where('role', 'admin')->latest()->get();

        // ── Transaksi terbaru ──────────────────────────────────────────────────
        $transaksiTerbaru = Transaksi::with('nasabah.user')
            ->latest()->take(6)->get();

        return view('super-admin.dashboard.index', compact(
            'totalNasabah', 'totalAdminAktif', 'totalTransaksi', 'totalNilaiSetoran',
            'setoranBulanIni', 'penarikanMenunggu',
            'chartLabels', 'chartNilai', 'chartBerat',
            'admins', 'transaksiTerbaru'
        ));
    }
}
