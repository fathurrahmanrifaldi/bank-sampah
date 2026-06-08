<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail {
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'status', 'google_id', 'email_verified_at'];
    protected $hidden   = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** Cek apakah user adalah super admin (Ketua RW) */
    public function isSuperAdmin(): bool {
        return $this->role === 'super_admin';
    }

    /** Cek apakah user adalah admin (Petugas Lapangan) */
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    /** Cek apakah akun sedang aktif */
    public function isAktif(): bool {
        return $this->status !== 'nonaktif';
    }

    /** Cek apakah email sudah terverifikasi */
    public function isVerified(): bool {
        return $this->email_verified_at !== null;
    }

    /** Relasi: satu user punya satu data nasabah */
    public function nasabah() {
        return $this->hasOne(Nasabah::class);
    }
}