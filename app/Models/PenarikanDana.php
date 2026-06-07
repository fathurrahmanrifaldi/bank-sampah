<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenarikanDana extends Model {

    protected $table    = 'penarikan_dana';
    protected $fillable = [
        'nasabah_id', 'jumlah', 'jenis', 'tanggal_diminta',
        'tanggal_pencairan', 'catatan_nasabah', 'status',
        'catatan_admin', 'diproses_oleh', 'transaksi_id',
    ];
    protected $casts = [
        'tanggal_diminta'   => 'date',
        'tanggal_pencairan' => 'date',
        'jumlah'            => 'decimal:2',
    ];

    public function nasabah() {
        return $this->belongsTo(Nasabah::class);
    }

    public function prosesOleh() {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function transaksi() {
        return $this->belongsTo(Transaksi::class);
    }

    // Helper status labels
    public function statusLabel(): string {
        return match($this->status) {
            'menunggu'  => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => '-',
        };
    }

    public function statusColor(): string {
        return match($this->status) {
            'menunggu'  => 'warning',
            'disetujui' => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }

    public function jenisLabel(): string {
        return match($this->jenis) {
            'segera'     => 'Cair Sekarang',
            'terjadwal'  => 'Terjadwal',
            default      => '-',
        };
    }
}
