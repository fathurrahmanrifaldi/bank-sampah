<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->decimal('skor', 10, 6)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->decimal('skor', 8, 2)->default(0)->change();
        });
    }
};
