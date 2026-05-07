<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Penilaian, Nasabah};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller {

    public function index(Request $request) {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $hasil = Penilaian::with('nasabah.user')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderByDesc('skor')
            ->get();

        return view('admin.penilaian.index',
            compact('hasil', 'bulan', 'tahun'));
    }

    /**
     * Hitung penilaian nasabah terbaik untuk bulan tertentu.
     *
     * Formula skor (skala 0–100):
     *   skor = (normBerat × 50) + (normFrekuensi × 30) + (normNilai × 20)
     *
     * Normalisasi min-max: nilai_i / max_nilai × 100
     */
    public function hitung(Request $request) {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Ambil data mentah per nasabah untuk bulan ini
        $data = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->whereMonth('transaksi.tanggal', $bulan)
            ->whereYear('transaksi.tanggal', $tahun)
            ->groupBy('transaksi.nasabah_id')
            ->select(
                'transaksi.nasabah_id',
                DB::raw('SUM(detail_transaksi.berat_kg) as total_berat'),
                DB::raw('COUNT(DISTINCT transaksi.id) as jumlah_setor'),
                DB::raw('SUM(transaksi.total_nilai) as total_nilai')
            )->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada transaksi pada periode ini.');
        }

        // Hitung nilai max untuk normalisasi
        $maxBerat  = $data->max('total_berat')  ?: 1;
        $maxSetor  = $data->max('jumlah_setor') ?: 1;
        $maxNilai  = $data->max('total_nilai')  ?: 1;

        // Simpan/update penilaian tiap nasabah
        foreach ($data as $d) {
            $normBerat  = ($d->total_berat  / $maxBerat)  * 100;
            $normSetor  = ($d->jumlah_setor / $maxSetor)  * 100;
            $normNilai  = ($d->total_nilai  / $maxNilai)  * 100;

            // Bobot: berat 50%, frekuensi 30%, nilai 20%
            $skor = ($normBerat * 0.5) + ($normSetor * 0.3) + ($normNilai * 0.2);

            Penilaian::updateOrCreate(
                ['nasabah_id' => $d->nasabah_id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'total_berat'  => $d->total_berat,
                    'jumlah_setor' => $d->jumlah_setor,
                    'total_nilai'  => $d->total_nilai,
                    'skor'         => round($skor, 2),
                    'predikat'     => $skor >= 80 ? 'Emas'
                                     : ($skor >= 60 ? 'Perak' : 'Perunggu'),
                ]
            );
        }

        return redirect()->route('admin.penilaian.index', [
            'bulan' => $bulan, 'tahun' => $tahun
        ])->with('success', 'Penilaian berhasil dihitung!');
    }
}