<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model {

    protected $table    = 'nasabah';
    protected $fillable = [
        'user_id', 'nik', 'alamat', 'no_hp', 'saldo'
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

    public function penarikanDana() {
        return $this->hasMany(PenarikanDana::class);
    }

}