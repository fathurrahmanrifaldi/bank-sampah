<?php
namespace Database\Seeders;

use App\Models\{User, KategoriSampah};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {

        // 1. Buat akun Super Admin (Ketua RW)
        User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Ketua',
                'password'          => Hash::make('superadmin123'),
                'role'              => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
    }
}