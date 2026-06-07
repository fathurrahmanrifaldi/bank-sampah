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
            'nasabah_id'       => 'required|exists:nasabah,id',
            'tanggal'          => 'required|date',
            'kategori_id'      => 'required|array|min:1',
            'kategori_id.*'    => 'exists:kategori_sampah,id',
            'berat_kg.*'       => 'required|numeric|min:0.001',
            'penarikan_jenis'  => 'nullable|in:segera,terjadwal,tidak',
            'tanggal_penarikan'=> 'required_if:penarikan_jenis,terjadwal|nullable|date|after_or_equal:today',
        ], [
            'tanggal_penarikan.required_if'     => 'Tanggal pencairan wajib diisi untuk penarikan terjadwal.',
            'tanggal_penarikan.after_or_equal'  => 'Tanggal pencairan tidak boleh sebelum hari ini.',
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
            $nasabah = Nasabah::find($request->nasabah_id);
            $nasabah->increment('saldo', $totalNilai);

            // ✅ Proses penarikan dana jika diminta
            $penarikanJenis = $request->penarikan_jenis;
            if ($penarikanJenis && $penarikanJenis !== 'tidak') {
                $tanggalDiminta = $penarikanJenis === 'segera'
                    ? \Carbon\Carbon::parse($request->tanggal)
                    : \Carbon\Carbon::parse($request->tanggal_penarikan);

                if ($penarikanJenis === 'segera') {
                    // Cair langsung – potong saldo, langsung disetujui
                    $nasabah->decrement('saldo', $totalNilai);
                    \App\Models\PenarikanDana::create([
                        'nasabah_id'        => $nasabah->id,
                        'jumlah'            => $totalNilai,
                        'jenis'             => 'segera',
                        'tanggal_diminta'   => $tanggalDiminta,
                        'tanggal_pencairan' => $tanggalDiminta,
                        'status'            => 'disetujui',
                        'diproses_oleh'     => auth()->id(),
                        'transaksi_id'      => $transaksi->id,
                    ]);
                } else {
                    // Terjadwal – tunggu konfirmasi admin pada hari H
                    \App\Models\PenarikanDana::create([
                        'nasabah_id'      => $nasabah->id,
                        'jumlah'          => $totalNilai,
                        'jenis'           => 'terjadwal',
                        'tanggal_diminta' => $tanggalDiminta,
                        'status'          => 'menunggu',
                        'transaksi_id'    => $transaksi->id,
                    ]);
                }
            }
        });

        $pesanTambahan = '';
        if ($request->penarikan_jenis === 'segera') {
            $pesanTambahan = ' Dana langsung dicairkan ke nasabah.';
        } elseif ($request->penarikan_jenis === 'terjadwal') {
            $pesanTambahan = ' Penarikan dijadwalkan pada ' . \Carbon\Carbon::parse($request->tanggal_penarikan)->format('d M Y') . '.';
        }

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil dicatat dan saldo diperbarui!' . $pesanTambahan);
    }

    public function show(Transaksi $transaksi) {
        $transaksi->load(['nasabah.user', 'detail.kategori', 'admin']);
        return view('admin.transaksi.show', compact('transaksi'));
    }
}