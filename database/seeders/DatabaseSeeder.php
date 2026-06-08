<?php
namespace Database\Seeders;

use App\Models\{User, KategoriSampah};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {

        // 1. Buat akun Super Admin (Ketua RW)
        User::firstOrCreate(
            ['email' => 'superadmin@banksampah.id'],
            [
                'name'              => 'Ketua RW 042',
                'password'          => Hash::make('superadmin123'),
                'role'              => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Buat akun Admin (Petugas Lapangan)
        User::firstOrCreate(
            ['email' => 'admin@banksampah.id'],
            [
                'name'              => 'Admin Bank Sampah',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Buat kategori sampah awal
        $kategori = [
            ['nama_kategori' => 'Botol Plastik',  'jenis' => 'anorganik',          'harga_per_kg' => 3000],
            ['nama_kategori' => 'Kardus / Kertas', 'jenis' => 'anorganik',          'harga_per_kg' => 1500],
            ['nama_kategori' => 'Kaleng Besi',     'jenis' => 'anorganik',          'harga_per_kg' => 5000],
            ['nama_kategori' => 'Sampah Organik',  'jenis' => 'organik',            'harga_per_kg' =>  500],
            ['nama_kategori' => 'Minyak Jelantah', 'jenis' => 'minyak_bekas',       'harga_per_kg' => 6000],
            ['nama_kategori' => 'Residu',           'jenis' => 'tidak_dapat_diolah', 'harga_per_kg' =>    0],
        ];

        foreach ($kategori as $k) {
            KategoriSampah::firstOrCreate(
                ['nama_kategori' => $k['nama_kategori']],
                $k
            );
        }
    }
}
