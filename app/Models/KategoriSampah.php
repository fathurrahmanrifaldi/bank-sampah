<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model {

    protected $table    = 'kategori_sampah';
    protected $fillable = ['nama_kategori', 'jenis', 'harga_per_kg', 'keterangan'];

    public function detailTransaksi() {
        return $this->hasMany(DetailTransaksi::class, 'kategori_id');
    }
}