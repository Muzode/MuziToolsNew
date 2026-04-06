<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Peminjam
            $table->foreignId('tool_id')->constrained('tools');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali_aktual')->nullable();
            // Status: pending, disetujui, ditolak, kembali
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'kembali'])->default('pending');
            $table->foreignId('petugas_id')->nullable()->constrained('users'); // Siapa yang menyetujui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
