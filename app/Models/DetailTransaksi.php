<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model {

    protected $table    = 'detail_transaksi';
    protected $fillable = ['transaksi_id', 'kategori_id', 'berat_kg', 'nilai'];

    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function kategori() {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }
}