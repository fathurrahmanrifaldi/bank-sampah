<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'nasabah_id',
        'semester',
        'tahun',
        // Nilai mentah kriteria SAW
        'konsistensi',        // C1 - bobot 50%
        'total_berat',        // C2 - bobot 30%
        'keragaman_kategori', // C3 - bobot 20%
        'tren_pertumbuhan',   // Kolom lama, tidak dipakai pada SAW terbaru.
        // Nilai normalisasi SAW
        'norm_konsistensi',
        'norm_total_berat',
        'norm_keragaman',
        'norm_tren',
        // Hasil akhir
        'skor',
        'predikat',
    ];

    protected $casts = [
        'konsistensi'        => 'float',
        'total_berat'        => 'float',
        'keragaman_kategori' => 'float',
        'tren_pertumbuhan'   => 'float',
        'norm_konsistensi'   => 'float',
        'norm_total_berat'   => 'float',
        'norm_keragaman'     => 'float',
        'norm_tren'          => 'float',
        'skor'               => 'float',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Label semester yang mudah dibaca.
     */
    public function getLabelSemesterAttribute(): string
    {
        return $this->semester === 1
            ? 'Semester I (Jan – Jun)'
            : 'Semester II (Jul – Des)';
    }
}
