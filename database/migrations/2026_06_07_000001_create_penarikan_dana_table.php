<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penarikan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah')->onDelete('cascade');
            $table->decimal('jumlah', 10, 2);
            $table->enum('jenis', ['segera', 'terjadwal'])->default('segera');
            $table->date('tanggal_diminta');
            $table->date('tanggal_pencairan')->nullable();
            $table->text('catatan_nasabah')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('penarikan_dana');
    }
};
