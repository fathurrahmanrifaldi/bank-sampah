<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Tambahkan nilai 'super_admin' ke kolom enum role pada tabel users.
     */
    public function up(): void {
        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'nasabah', 'super_admin') NOT NULL DEFAULT 'nasabah'"
        );
    }

    public function down(): void {
        // Kembalikan ke enum asal (pastikan tidak ada data super_admin sebelum rollback)
        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'nasabah') NOT NULL DEFAULT 'nasabah'"
        );
    }
};
