<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Bersihkan data lama (dari sistem bulanan)
        DB::table('penilaian')->truncate();

        Schema::table('penilaian', function (Blueprint $table) {
            // Hapus kolom sistem lama
            $table->dropColumn(['bulan', 'jumlah_setor', 'total_nilai']);
        });

        Schema::table('penilaian', function (Blueprint $table) {
            // Tambah kolom semester (pengganti bulan)
            $table->tinyInteger('semester')->after('tahun')->comment('1 = Jan-Jun, 2 = Jul-Des');

            // Kolom nilai mentah tiap kriteria SAW
            $table->decimal('konsistensi', 8, 4)->default(0)->after('semester')
                  ->comment('C1: rata-rata transaksi per bulan (bobot 30%)');
            $table->decimal('total_berat', 10, 3)->default(0)->change();
            $table->decimal('keragaman_kategori', 8, 4)->default(0)->after('total_berat')
                  ->comment('C3: jumlah kategori unik (bobot 20%)');
            $table->decimal('tren_pertumbuhan', 10, 4)->default(0)->after('keragaman_kategori')
                  ->comment('C4: selisih rata-rata berat 3 bulan akhir vs awal (bobot 25%), bisa negatif');

            // Kolom nilai normalisasi SAW per kriteria (untuk transparansi)
            $table->decimal('norm_konsistensi', 8, 6)->default(0)->after('tren_pertumbuhan');
            $table->decimal('norm_total_berat', 8, 6)->default(0)->after('norm_konsistensi');
            $table->decimal('norm_keragaman', 8, 6)->default(0)->after('norm_total_berat');
            $table->decimal('norm_tren', 8, 6)->default(0)->after('norm_keragaman');

            // Unique constraint agar tidak duplikat per nasabah per semester+tahun
            $table->unique(['nasabah_id', 'semester', 'tahun'], 'penilaian_nasabah_semester_tahun_unique');
        });
    }

    public function down(): void {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropUnique('penilaian_nasabah_semester_tahun_unique');
            $table->dropColumn([
                'semester', 'konsistensi', 'keragaman_kategori',
                'tren_pertumbuhan', 'norm_konsistensi', 'norm_total_berat',
                'norm_keragaman', 'norm_tren',
            ]);
            $table->integer('bulan')->after('nasabah_id');
            $table->integer('jumlah_setor')->default(0);
            $table->decimal('total_nilai', 10, 2)->default(0);
        });
    }
};
