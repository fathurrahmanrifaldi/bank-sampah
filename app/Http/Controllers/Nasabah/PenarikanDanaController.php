<?php
namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenarikanDanaController extends Controller
{
    /**
     * Riwayat pengajuan penarikan milik nasabah yang sedang login.
     */
    public function index()
    {
        $nasabah = auth()->user()->nasabah;

        if (!$nasabah) {
            return redirect()->route('nasabah.complete-profile')
                ->with('warning', 'Harap lengkapi profil Anda terlebih dahulu.');
        }

        $penarikan = $nasabah->penarikanDana()
            ->latest()
            ->paginate(15);

        // Cek apakah ada pengajuan yang masih menunggu
        $adaMenunggu = $nasabah->penarikanDana()
            ->where('status', 'menunggu')
            ->exists();

        return view('nasabah.penarikan.index', compact('penarikan', 'adaMenunggu', 'nasabah'));
    }

    /**
     * Form pengajuan penarikan mandiri oleh nasabah.
     */
    public function create()
    {
        $nasabah = auth()->user()->nasabah;

        if (!$nasabah) {
            return redirect()->route('nasabah.complete-profile');
        }

        // Tidak boleh ajukan baru jika masih ada yang menunggu
        $adaMenunggu = $nasabah->penarikanDana()
            ->where('status', 'menunggu')
            ->exists();

        if ($adaMenunggu) {
            return redirect()->route('nasabah.penarikan.index')
                ->with('error', 'Anda masih memiliki pengajuan penarikan yang sedang menunggu persetujuan.');
        }

        return view('nasabah.penarikan.create', compact('nasabah'));
    }

    /**
     * Simpan pengajuan penarikan mandiri.
     */
    public function store(Request $request)
    {
        $nasabah = auth()->user()->nasabah;

        if (!$nasabah) {
            return redirect()->route('nasabah.complete-profile');
        }

        // Cek duplikasi menunggu
        if ($nasabah->penarikanDana()->where('status', 'menunggu')->exists()) {
            return redirect()->route('nasabah.penarikan.index')
                ->with('error', 'Anda masih memiliki pengajuan penarikan yang sedang menunggu.');
        }

        $request->validate([
            'jumlah'           => 'required|numeric|min:10000|max:' . $nasabah->saldo,
            'jenis'            => 'required|in:segera,terjadwal',
            'tanggal_diminta'  => 'required_if:jenis,terjadwal|nullable|date|after_or_equal:today',
            'catatan_nasabah'  => 'nullable|string|max:500',
        ], [
            'jumlah.max'             => 'Nominal melebihi saldo Anda (Rp ' . number_format($nasabah->saldo, 0, ',', '.') . ').',
            'jumlah.min'             => 'Minimal penarikan adalah Rp 10.000.',
            'tanggal_diminta.required_if' => 'Tanggal pencairan wajib diisi untuk penarikan terjadwal.',
            'tanggal_diminta.after_or_equal' => 'Tanggal pencairan tidak boleh sebelum hari ini.',
        ]);

        $tanggal = $request->jenis === 'segera'
            ? Carbon::today()
            : Carbon::parse($request->tanggal_diminta);

        PenarikanDana::create([
            'nasabah_id'      => $nasabah->id,
            'jumlah'          => $request->jumlah,
            'jenis'           => $request->jenis,
            'tanggal_diminta' => $tanggal,
            'catatan_nasabah' => $request->catatan_nasabah,
            'status'          => 'menunggu',
        ]);

        return redirect()->route('nasabah.penarikan.index')
            ->with('success', 'Pengajuan penarikan dana berhasil dikirim. Tunggu konfirmasi dari admin.');
    }
}
