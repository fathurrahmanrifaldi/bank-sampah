<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, DetailTransaksi, Nasabah, KategoriSampah, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Log};
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    public function edit(Transaksi $transaksi) {
        $transaksi->load(['nasabah.user', 'detail.kategori']);
        $nasabah  = Nasabah::with('user')->get();
        $kategori = KategoriSampah::all();
        return view('admin.transaksi.edit', compact('transaksi', 'nasabah', 'kategori'));
    }

    public function update(Request $request, Transaksi $transaksi) {
        $request->validate([
            'tanggal'       => 'required|date',
            'kategori_id'   => 'required|array|min:1',
            'kategori_id.*' => 'exists:kategori_sampah,id',
            'berat_kg.*'    => 'required|numeric|min:0.001',
            'catatan'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $transaksi) {
            $nilaiLama = $transaksi->total_nilai;

            // Hapus detail lama
            $transaksi->detail()->delete();

            // Hitung ulang
            $totalNilai = 0;
            foreach ($request->kategori_id as $i => $kategoriId) {
                $kat        = KategoriSampah::findOrFail($kategoriId);
                $beratKg    = (float) $request->berat_kg[$i];
                $nilai      = $beratKg * $kat->harga_per_kg;
                $totalNilai += $nilai;

                $transaksi->detail()->create([
                    'kategori_id' => $kategoriId,
                    'berat_kg'    => $beratKg,
                    'nilai'       => $nilai,
                ]);
            }

            // Sesuaikan saldo: kurangi nilai lama, tambah nilai baru
            $selisih = $totalNilai - $nilaiLama;
            $transaksi->nasabah->increment('saldo', $selisih);

            $transaksi->update([
                'nasabah_id'  => $transaksi->nasabah_id, // nasabah tidak bisa diubah
                'tanggal'     => $request->tanggal,
                'total_nilai' => $totalNilai,
                'catatan'     => $request->catatan,
            ]);
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi #' . $transaksi->id . ' berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi) {
        DB::transaction(function () use ($transaksi) {
            // Kembalikan saldo nasabah
            $transaksi->nasabah->decrement('saldo', $transaksi->total_nilai);

            // Hapus detail lalu header
            $transaksi->detail()->delete();
            $transaksi->delete();
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus dan saldo nasabah disesuaikan.');
    }

    // ══════════════════════════════════════════════════
    // Import Data Setoran dari Excel
    // ══════════════════════════════════════════════════
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $path = $request->file('file_excel')->getRealPath();
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

            // Mapping nama bulan Indonesia → angka
            $bulanMap = [
                'januari' => 1, 'februari' => 2, 'february' => 2,
                'maret'   => 3, 'april'    => 4, 'mei'      => 5,
                'juni'    => 6, 'juli'     => 7, 'agustus'  => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11,
                'desember' => 12,
            ];

            // Bangun cache kategori: key = nama_kategori PERSIS sama dengan di database
            $kategoriCache = [];
            KategoriSampah::all()->each(function ($k) use (&$kategoriCache) {
                $kategoriCache[$k->nama_kategori] = $k;
            });

            $stats = [
                'transaksi_dibuat'     => 0,
                'nasabah_baru'         => 0,
                'baris_dilewati'       => 0,
                'kategori_tidak_cocok' => [],
            ];

            // Proses setiap sheet
            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheetNorm = $this->normalizeStr($sheetName);
                if (!array_key_exists($sheetNorm, $bulanMap)) {
                    continue; // Lewati sheet yang bukan nama bulan
                }

                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows  = $sheet->toArray(null, true, true, false);

                if (empty($rows)) continue;

                // Baris pertama = header — dibaca APA ADANYA (sama persis)
                $headers = array_map(fn($h) => trim((string)$h), $rows[0]);

                // Identifikasi indeks kolom tetap (case-insensitive hanya untuk kolom fixed)
                $colTanggal = false;
                $colNama    = false;
                foreach ($headers as $idx => $h) {
                    $hLower = strtolower($h);
                    if ($colTanggal === false && $hLower === 'tanggal') $colTanggal = $idx;
                    if ($colNama    === false && in_array($hLower, ['nama nasabah', 'nama_nasabah', 'nama'])) $colNama = $idx;
                }

                if ($colNama === false) {
                    continue; // Sheet tidak punya kolom nama nasabah
                }

                // Identifikasi kolom kategori: semua kolom SELAIN kolom fixed
                // Nama kolom dipakai APA ADANYA untuk dicocokkan dengan nama_kategori di DB
                $fixedIdxs = array_filter([$colTanggal, $colNama]);
                $fixedLower = ['tanggal', 'no', 'nama nasabah', 'nama_nasabah', 'nama'];
                $kategoriCols = [];
                foreach ($headers as $idx => $h) {
                    if (in_array($idx, $fixedIdxs)) continue;
                    if (in_array(strtolower(trim($h)), $fixedLower) || trim($h) === '') continue;
                    $kategoriCols[$idx] = $h; // simpan nama kolom asli
                }

                DB::transaction(function () use ($rows, $headers, $colTanggal, $colNama, $kategoriCols, $kategoriCache, &$stats) {
                    foreach (array_slice($rows, 1) as $row) {
                        // Ambil nama nasabah
                        $namaNasabah = trim((string)($row[$colNama] ?? ''));
                        if (empty($namaNasabah)) {
                            $stats['baris_dilewati']++;
                            continue;
                        }

                        // Ambil tanggal
                        $tanggalRaw = $colTanggal !== false ? $row[$colTanggal] : null;
                        $tanggal    = $this->parseTanggal($tanggalRaw);
                        if (!$tanggal) {
                            $stats['baris_dilewati']++;
                            continue;
                        }

                        // Cari / buat nasabah
                        $nasabah = $this->cariAtauBuatNasabah($namaNasabah, $stats);

                        // Kumpulkan detail kategori
                        $details    = [];
                        $totalNilai = 0;

                        foreach ($kategoriCols as $idx => $namaKolom) {
                            $beratKg = (float)($row[$idx] ?? 0);
                            if ($beratKg <= 0) continue;

                            // Cocokkan PERSIS dengan nama_kategori di database
                            $kategori = $kategoriCache[$namaKolom] ?? null;

                            if (!$kategori) {
                                if (!in_array($namaKolom, $stats['kategori_tidak_cocok'])) {
                                    $stats['kategori_tidak_cocok'][] = $namaKolom;
                                }
                                continue;
                            }

                            $nilai       = $beratKg * $kategori->harga_per_kg;
                            $totalNilai += $nilai;
                            $details[]  = [
                                'kategori_id' => $kategori->id,
                                'berat_kg'    => $beratKg,
                                'nilai'       => $nilai,
                            ];
                        }

                        if (empty($details)) {
                            $stats['baris_dilewati']++;
                            continue;
                        }

                        // Simpan transaksi
                        $transaksi = Transaksi::create([
                            'nasabah_id'  => $nasabah->id,
                            'admin_id'    => auth()->id(),
                            'tanggal'     => $tanggal->toDateString(),
                            'total_nilai' => $totalNilai,
                            'catatan'     => 'Import dari Excel',
                        ]);

                        foreach ($details as $d) {
                            $transaksi->detail()->create($d);
                        }

                        $nasabah->increment('saldo', $totalNilai);
                        $stats['transaksi_dibuat']++;
                    }
                });
            }

            return redirect()->route('admin.transaksi.index')
                ->with('import_stats', $stats);

        } catch (\Throwable $e) {
            Log::error('Import Excel error: ' . $e->getMessage());
            return redirect()->route('admin.transaksi.index')
                ->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function normalizeStr(string $str): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($str)));
    }

    private function findColIndex(array $headers, array $candidates): int|false
    {
        foreach ($candidates as $c) {
            $idx = array_search($c, $headers);
            if ($idx !== false) return $idx;
        }
        return false;
    }

    private function parseTanggal(mixed $raw): ?Carbon
    {
        if (empty($raw)) return null;

        if ($raw instanceof Carbon) return $raw;
        if ($raw instanceof \DateTimeInterface) return Carbon::instance($raw);

        // PhpSpreadsheet converts dates to Unix timestamp floats — check
        if (is_float($raw) || (is_int($raw) && $raw > 30000)) {
            try {
                return Carbon::createFromTimestamp(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($raw));
            } catch (\Throwable) {}
        }

        $str = trim((string)$raw);
        if (empty($str)) return null;

        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'd M Y', 'd F Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $str)->startOfDay();
            } catch (\Throwable) {}
        }

        try {
            return Carbon::parse($str);
        } catch (\Throwable) {
            return null;
        }
    }

    private function cariAtauBuatNasabah(string $nama, array &$stats): Nasabah
    {
        $user = User::whereRaw('LOWER(name) = ?', [strtolower($nama)])->first();

        if (!$user) {
            // Slug nama: hilangkan spasi & karakter aneh → huruf kecil
            $namaSlug = Str::slug($nama, '');   // e.g. "Budi Santoso" → "budisantoso"

            // Email: namanasabah@gmail.com (pastikan unik)
            $email   = $namaSlug . '@gmail.com';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $namaSlug . $counter . '@gmail.com';
                $counter++;
            }

            // NIK: 16 digit angka acak (tidak boleh diawali 0)
            $nik = (string) random_int(1000000000000000, 9999999999999999);
            while (Nasabah::where('nik', $nik)->exists()) {
                $nik = (string) random_int(1000000000000000, 9999999999999999);
            }

            $user = User::create([
                'name'              => $nama,
                'email'             => $email,
                'password'          => Hash::make($namaSlug . '1234'),
                'role'              => 'nasabah',
                'email_verified_at' => now(),
            ]);

            Nasabah::create([
                'user_id' => $user->id,
                'nik'     => $nik,
                'alamat'  => '',
                'no_hp'   => '',
                'saldo'   => 0,
            ]);

            $stats['nasabah_baru']++;
        }

        return $user->nasabah ?? Nasabah::firstOrCreate(
            ['user_id' => $user->id],
            ['nik' => '', 'alamat' => '', 'no_hp' => '', 'saldo' => 0]
        );
    }
}