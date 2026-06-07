<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PenarikanDana, Nasabah};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenarikanDanaController extends Controller
{
    /**
     * Daftar semua pengajuan penarikan dengan filter status.
     */
    public function index(Request $request)
    {
        $query = PenarikanDana::with(['nasabah.user', 'prosesOleh'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penarikan       = $query->paginate(15)->withQueryString();
        $totalMenunggu   = PenarikanDana::where('status', 'menunggu')->count();
        $totalDisetujui  = PenarikanDana::where('status', 'disetujui')->count();
        $totalDitolak    = PenarikanDana::where('status', 'ditolak')->count();

        return view('admin.penarikan-dana.index',
            compact('penarikan', 'totalMenunggu', 'totalDisetujui', 'totalDitolak'));
    }

    /**
     * Setujui pengajuan penarikan → potong saldo nasabah.
     */
    public function approve(Request $request, $id)
    {
        $penarikan = PenarikanDana::with('nasabah')->findOrFail($id);

        if ($penarikan->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $nasabah = $penarikan->nasabah;

        if ($nasabah->saldo < $penarikan->jumlah) {
            return back()->with('error', 'Saldo nasabah tidak mencukupi untuk dicairkan.');
        }

        DB::transaction(function () use ($penarikan, $nasabah) {
            // Potong saldo
            $nasabah->decrement('saldo', $penarikan->jumlah);

            // Update status penarikan
            $penarikan->update([
                'status'            => 'disetujui',
                'tanggal_pencairan' => Carbon::today(),
                'diproses_oleh'     => auth()->id(),
            ]);
        });

        return back()->with('success', 'Penarikan dana berhasil disetujui dan saldo nasabah telah dipotong.');
    }

    /**
     * Tolak pengajuan penarikan.
     */
    public function reject(Request $request, $id)
    {
        $penarikan = PenarikanDana::findOrFail($id);

        if ($penarikan->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $penarikan->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Pengajuan penarikan berhasil ditolak.');
    }
}
