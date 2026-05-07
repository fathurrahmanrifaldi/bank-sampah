<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('total_berat', 10, 3)->default(0);
            $table->integer('jumlah_setor')->default(0);
            $table->decimal('total_nilai', 10, 2)->default(0);
            $table->decimal('skor', 8, 2)->default(0);
            $table->string('predikat')->nullable(); // Emas, Perak, Perunggu
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('penilaian');
    }
};