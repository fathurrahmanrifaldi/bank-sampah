<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Penilaian, Nasabah};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    // ─── Bobot SAW ────────────────────────────────────────────────────────────
    const BOBOT = [
        'konsistensi'        => 0.50, // C1: Konsistensi setoran (benefit)
        'total_berat'        => 0.30, // C2: Total berat sampah (benefit)
        'keragaman_kategori' => 0.20, // C3: Keragaman kategori (benefit)
    ];

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Default: semester sekarang
        $bulanSekarang = now()->month;
        $semesterDefault = $bulanSekarang <= 6 ? 1 : 2;

        $semester = (int) $request->get('semester', $semesterDefault);
        $tahun    = (int) $request->get('tahun', now()->year);

        // Pastikan semester valid
        $semester = in_array($semester, [1, 2]) ? $semester : 1;

        $hasil = Penilaian::with('nasabah.user')
            ->where('semester', $semester)
            ->where('tahun', $tahun)
            ->orderByDesc('skor')
            ->get();

        return view('admin.penilaian.index', compact('hasil', 'semester', 'tahun'));
    }

    // ─── Hitung SAW ───────────────────────────────────────────────────────────

    /**
     * Hitung penilaian nasabah terbaik untuk satu semester (6 bulan).
     *
     * Metode: Simple Additive Weighting (SAW) – semua kriteria benefit.
     *
     * Kriteria & bobot:
     *   C1 – Konsistensi setoran  : 50%  → rata-rata transaksi/bulan selama 6 bln
     *   C2 – Total berat sampah   : 30%  → total kg dalam semester
     *   C3 – Keragaman kategori   : 20%  → COUNT(DISTINCT kategori_id)
     *
     * Normalisasi (benefit): r_ij = x_ij / max(x_j)
     */
    public function hitung(Request $request)
    {
        $request->validate([
            'semester' => 'required|integer|in:1,2',
            'tahun'    => 'required|integer|min:2020',
        ]);

        $semester = (int) $request->semester;
        $tahun    = (int) $request->tahun;

        // Tentukan rentang bulan semester
        [$bulanAwal, $bulanAkhir]      = $semester === 1 ? [1, 6]  : [7, 12];

        // ── C1 & C2: Konsistensi + Total Berat ─────────────────────────────
        // Konsistensi = jumlah bulan unik yang ada transaksi (dari total 6 bulan)
        // (pendekatan: COUNT(DISTINCT month) / 6 * 6 = jumlah bulan aktif)
        $dataUtama = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->whereYear('transaksi.tanggal', $tahun)
            ->whereBetween(DB::raw('MONTH(transaksi.tanggal)'), [$bulanAwal, $bulanAkhir])
            ->groupBy('transaksi.nasabah_id')
            ->select(
                'transaksi.nasabah_id',
                // C1: rata-rata transaksi unik per bulan (jumlah transaksi / 6 bulan)
                DB::raw('COUNT(DISTINCT transaksi.id) / 6.0 as konsistensi'),
                // C2: total berat
                DB::raw('SUM(detail_transaksi.berat_kg) as total_berat'),
                // C3: keragaman kategori unik
                DB::raw('COUNT(DISTINCT detail_transaksi.kategori_id) as keragaman_kategori')
            )
            ->get()
            ->keyBy('nasabah_id');

        if ($dataUtama->isEmpty()) {
            return back()->with('error', 'Tidak ada transaksi pada semester ini.');
        }

        // ── Susun data mentah semua nasabah ──────────────────────────────────
        $mentah = collect();
        foreach ($dataUtama as $nasabahId => $d) {
            $mentah->push([
                'nasabah_id'         => $nasabahId,
                'konsistensi'        => (float) $d->konsistensi,
                'total_berat'        => (float) $d->total_berat,
                'keragaman_kategori' => (float) $d->keragaman_kategori,
            ]);
        }

        // ── Normalisasi SAW (benefit) ─────────────────────────────────────────
        $maxC1 = max($mentah->max('konsistensi'), 1e-9);
        $maxC2 = max($mentah->max('total_berat'), 1e-9);
        $maxC3 = max($mentah->max('keragaman_kategori'), 1e-9);

        // ── Hitung skor SAW & simpan ──────────────────────────────────────────
        $skorList = [];
        foreach ($mentah as $idx => $d) {
            $normC1 = $d['konsistensi']        / $maxC1;
            $normC2 = $d['total_berat']        / $maxC2;
            $normC3 = $d['keragaman_kategori'] / $maxC3;

            $skor = ($normC1 * self::BOBOT['konsistensi'])
                  + ($normC2 * self::BOBOT['total_berat'])
                  + ($normC3 * self::BOBOT['keragaman_kategori']);

            $skorList[$d['nasabah_id']] = [
                'data'  => $d,
                'norms' => compact('normC1', 'normC2', 'normC3'),
                'skor'  => round($skor, 6),
            ];
        }

        // Urutkan skor tertinggi dulu untuk menentukan rank
        arsort($skorList);
        $rank = 1;

        foreach ($skorList as $nasabahId => $item) {
            $d        = $item['data'];
            $norms    = $item['norms'];

            Penilaian::updateOrCreate(
                ['nasabah_id' => $nasabahId, 'semester' => $semester, 'tahun' => $tahun],
                [
                    'konsistensi'        => $d['konsistensi'],
                    'total_berat'        => $d['total_berat'],
                    'keragaman_kategori' => $d['keragaman_kategori'],
                    'tren_pertumbuhan'   => 0,
                    'norm_konsistensi'   => round($norms['normC1'], 6),
                    'norm_total_berat'   => round($norms['normC2'], 6),
                    'norm_keragaman'     => round($norms['normC3'], 6),
                    'norm_tren'          => 0,
                    'skor'               => $item['skor'],
                    'predikat'           => null,
                ]
            );

            $rank++;
        }

        return redirect()->route('admin.penilaian.index', [
            'semester' => $semester,
            'tahun'    => $tahun,
        ])->with('success', 'Penilaian SAW berhasil dihitung untuk ' .
            ($semester === 1 ? 'Semester I' : 'Semester II') . " $tahun!");
    }
}