<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanPengepul extends Model {

    protected $table    = 'penjualan_pengepul';
    protected $fillable = ['tanggal_jual', 'total_uang', 'catatan', 'admin_id'];
    protected $casts    = ['tanggal_jual' => 'date'];

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
