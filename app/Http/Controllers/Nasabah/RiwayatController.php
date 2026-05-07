<?php
namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;

class RiwayatController extends Controller
{
    public function index()
    {
        $nasabah = auth()->user()->nasabah;

        $transaksi = $nasabah->transaksi()
            ->with('detail.kategori')
            ->latest('tanggal')
            ->paginate(15);

        return view('nasabah.riwayat.index', compact('transaksi'));
    }
}