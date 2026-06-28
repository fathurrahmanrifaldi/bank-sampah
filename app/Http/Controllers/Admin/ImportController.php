<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSampah;
use App\Models\Nasabah;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    // ── Peta nama bulan Indonesia → nomor bulan ──────────────────────────────
    private const MONTH_MAP = [
        'jan' => 1, 'feb' => 2,  'mar' => 3,  'apr' => 4,
        'mei' => 5, 'may' => 5,  'jun' => 6,  'jul' => 7,
        'agu' => 8, 'aug' => 8,  'sep' => 9,  'okt' => 10,
        'oct' => 10,'nov' => 11, 'des' => 12, 'dec' => 12,
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Halaman Import
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        return view('admin.import.index');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Download Template CSV
    // ─────────────────────────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_nasabah.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'No', 'Nama Nasabah',
            'Sep 2025', 'Okt 2025', 'Nov 2025', 'Des 2025', 'Jan 2026', 'Feb 2026',
            'Total Berat (kg)', 'Total Keragaman Jenis Sampah',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM agar Excel tidak salah baca karakter Indonesia
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);
            fputcsv($file, ['1', 'Budi Santoso',   '2', '3', '1', '2', '3', '2', '45.50', '3']);
            fputcsv($file, ['2', 'Siti Rahayu',    '1', '1', '0', '1', '2', '1', '22.30', '2']);
            fputcsv($file, ['3', 'Agus Wijaya',    '0', '1', '1', '0', '1', '0', '10.00', '1']);
            fputcsv($file, ['4', 'Dewi Lestari',   '3', '2', '2', '3', '1', '2', '60.75', '4']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Proses Upload CSV
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'file.required' => 'File CSV wajib dipilih.',
            'file.mimes'    => 'Format file harus CSV (.csv). Simpan file Excel Anda sebagai "CSV UTF-8".',
            'file.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $adminId    = auth()->id();
        $categories = KategoriSampah::orderBy('id')->get();

        if ($categories->isEmpty()) {
            return back()->with('error', 'Tidak ada kategori sampah di database. Silakan tambahkan kategori sampah terlebih dahulu sebelum import.');
        }

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        // ── Baca & strip BOM ─────────────────────────────────────────────────
        $rawHeader = fgetcsv($handle);
        if (!$rawHeader) {
            fclose($handle);
            return back()->with('error', 'File CSV tidak valid atau kosong.');
        }
        // Strip BOM dari kolom pertama jika ada
        $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeader[0]);

        // ── Deteksi indeks kolom ─────────────────────────────────────────────
        $namaIdx      = null;
        $beratIdx     = null;
        $keragamanIdx = null;
        $monthCols    = [];   // [colIndex => ['month'=>int,'year'=>int,'label'=>string]]

        foreach ($rawHeader as $i => $col) {
            $col     = trim($col);
            $colLow  = strtolower($col);

            if (str_contains($colLow, 'nama nasabah')) {
                $namaIdx = $i;
            } elseif (str_contains($colLow, 'total berat')) {
                $beratIdx = $i;
            } elseif (str_contains($colLow, 'keragaman')) {
                $keragamanIdx = $i;
            } elseif (preg_match('/^([a-zA-Z]{3})\s+(\d{4})$/u', $col, $m)) {
                $monthKey = strtolower($m[1]);
                if (isset(self::MONTH_MAP[$monthKey])) {
                    $monthCols[$i] = [
                        'month' => self::MONTH_MAP[$monthKey],
                        'year'  => (int) $m[2],
                        'label' => $col,
                    ];
                }
            }
        }

        // ── Validasi struktur header ──────────────────────────────────────────
        $missingCols = [];
        if ($namaIdx === null)      $missingCols[] = '"Nama Nasabah"';
        if ($beratIdx === null)     $missingCols[] = '"Total Berat (kg)"';
        if ($keragamanIdx === null) $missingCols[] = '"Total Keragaman Jenis Sampah"';
        if (empty($monthCols))      $missingCols[] = 'kolom bulan (contoh: "Sep 2025")';

        if (!empty($missingCols)) {
            fclose($handle);
            return back()->with('error', 'Kolom tidak ditemukan: ' . implode(', ', $missingCols) . '. Gunakan template yang disediakan.');
        }

        // ── Proses baris data ─────────────────────────────────────────────────
        $results = [
            'sukses'         => 0,
            'dilewati'       => 0,
            'errors'         => [],
            'created_users'  => [],
        ];

        $rowNum = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                // Lewati baris kosong
                if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                    continue;
                }

                $nama       = trim($row[$namaIdx] ?? '');
                $totalBerat = (float) str_replace(',', '.', trim($row[$beratIdx] ?? '0'));
                $keragaman  = max(1, (int) trim($row[$keragamanIdx] ?? '1'));

                if ($nama === '') {
                    $results['errors'][] = "Baris {$rowNum}: Nama Nasabah kosong, dilewati.";
                    continue;
                }

                // ── Kumpulkan jumlah setoran per bulan ──────────────────────
                $monthSetoran = [];
                $totalSetoran = 0;
                foreach ($monthCols as $colIdx => $info) {
                    $count = max(0, (int) trim($row[$colIdx] ?? '0'));
                    if ($count > 0) {
                        $monthSetoran[$colIdx] = $count;
                        $totalSetoran += $count;
                    }
                }

                // Lewati nasabah tanpa aktivitas
                if ($totalSetoran === 0 || $totalBerat <= 0) {
                    $results['dilewati']++;
                    continue;
                }

                // ── Cari atau buat User & Nasabah ────────────────────────────
                $user = User::where('name', $nama)->where('role', 'nasabah')->first();

                if (!$user) {
                    $baseSlug = Str::slug($nama, '.');
                    $email    = $baseSlug . '.' . rand(1000, 9999) . '@import.local';
                    // Pastikan email unik
                    while (User::where('email', $email)->exists()) {
                        $email = $baseSlug . '.' . rand(1000, 9999) . '@import.local';
                    }

                    $user = User::create([
                        'name'              => $nama,
                        'email'             => $email,
                        'password'          => Hash::make('nasabah123'),
                        'role'              => 'nasabah',
                        'status'            => 'aktif',
                        'email_verified_at' => now(),
                    ]);

                    $nasabah = Nasabah::create([
                        'user_id' => $user->id,
                        'nik'     => 'IMP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                        'saldo'   => 0,
                    ]);

                    $results['created_users'][] = $nama;
                } else {
                    $nasabah = $user->nasabah;
                    if (!$nasabah) {
                        $nasabah = Nasabah::create([
                            'user_id' => $user->id,
                            'nik'     => 'IMP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                            'saldo'   => 0,
                        ]);
                    }
                }

                // ── Siapkan kategori untuk cycling ───────────────────────────
                // Gunakan sejumlah keragaman kategori pertama, cycling per transaksi
                $keragamanActual = min($keragaman, $categories->count());
                $usedCats        = $categories->values()->take($keragamanActual);

                $beratPerSetoran = round($totalBerat / $totalSetoran, 3);
                $setoranIndex    = 0;
                $totalNilaiNasabah = 0;

                // ── Buat transaksi per bulan ─────────────────────────────────
                foreach ($monthSetoran as $colIdx => $count) {
                    $info = $monthCols[$colIdx];
                    for ($i = 0; $i < $count; $i++) {
                        // Tanggal: hari ke-($i+1) di bulan tsb, maks tanggal 28
                        $tanggal = Carbon::create($info['year'], $info['month'], min($i + 1, 28));

                        // Pilih kategori secara cycling
                        $cat     = $usedCats[$setoranIndex % $keragamanActual];
                        $berat   = $beratPerSetoran;
                        $nilai   = round($berat * (float)($cat->harga_per_kg ?? 0), 2);

                        $transaksi = Transaksi::create([
                            'nasabah_id'  => $nasabah->id,
                            'admin_id'    => $adminId,
                            'tanggal'     => $tanggal->toDateString(),
                            'total_nilai' => $nilai,
                            'catatan'     => 'Import data historis',
                        ]);

                        DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'kategori_id'  => $cat->id,
                            'berat_kg'     => $berat,
                            'nilai'        => $nilai,
                        ]);

                        $totalNilaiNasabah += $nilai;
                        $setoranIndex++;
                    }
                }

                // Update saldo nasabah
                $nasabah->increment('saldo', $totalNilaiNasabah);

                $results['sukses']++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Terjadi kesalahan pada baris ' . $rowNum . ': ' . $e->getMessage());
        }

        fclose($handle);

        session(['import_results' => $results]);
        return redirect()->route('admin.import.index')->with('import_success', true);
    }
}
