<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {

    protected $table    = 'transaksi';
    protected $fillable = ['nasabah_id', 'admin_id', 'tanggal', 'total_nilai', 'catatan'];
    protected $casts    = ['tanggal' => 'date'];

    public function nasabah() {
        return $this->belongsTo(Nasabah::class);
    }
    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function detail() {
        return $this->hasMany(DetailTransaksi::class);
    }
}