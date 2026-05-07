<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model {

    protected $table    = 'nasabah';
    protected $fillable = [
        'user_id', 'no_rekening', 'alamat', 'no_hp', 'saldo'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function transaksi() {
        return $this->hasMany(Transaksi::class);
    }
    public function penilaian() {
        return $this->hasMany(Penilaian::class);
    }

    /** Generate nomor rekening otomatis: BS-2026-001 */
    public static function generateNoRekening(): string {
        $count = self::count() + 1;
        return 'BS-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}