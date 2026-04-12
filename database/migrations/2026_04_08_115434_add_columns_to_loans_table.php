<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Cek apakah kolom denda sudah ada
            if (!Schema::hasColumn('loans', 'denda')) {
                $table->decimal('denda', 12, 2)->nullable()->after('tanggal_kembali_aktual');
            }
            $table->text('keterangan_kondisi')->nullable()->after('denda_per_hari');
            $table->string('gambar_kondisi')->nullable()->after('keterangan_kondisi');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('denda', 'keterangan_kondisi', 'gambar_kondisi');
        });
    }
};
