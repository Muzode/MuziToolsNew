<?php
// app/Models/Loan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'tool_id',
        'quantity',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'petugas_id',
        'denda_per_hari',
        'denda',
        'keterangan_kondisi',
        'gambar_kondisi'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali_rencana' => 'datetime',
        'tanggal_kembali_aktual' => 'datetime',
        'denda' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Hitung denda keterlambatan
     * Denda Rp 5.000 per hari jika tanggal_kembali_aktual > tanggal_kembali_rencana
     */
    public function calculateDenda()
    {
        if (!$this->tanggal_kembali_aktual || !$this->tanggal_kembali_rencana) {
            return 0;
        }

        if ($this->tanggal_kembali_aktual->lte($this->tanggal_kembali_rencana)) {
            return 0;
        }

        $diffHours = $this->tanggal_kembali_rencana->diffInHours($this->tanggal_kembali_aktual);
        $hariTelat = (int) ceil($diffHours / 24);
        $dendaPerHari = $this->denda_per_hari ?? 5000;

        return $hariTelat * $dendaPerHari;
    }

    /**
     * Update denda ke database
     */
    public function updateDenda()
    {
        $denda = $this->calculateDenda();
        $this->update(['denda' => $denda]);
        return $denda;
    }

    /**
     * Cek apakah denda sudah dibayar
     */
    public function isDendaPaid()
    {
        return $this->payments()
            ->where('payment_type', 'denda')
            ->whereIn('status', ['settlement', 'capture'])
            ->exists();
    }

    /**
     * Get denda yang belum dibayar
     */
    public function getUnpaidDendaAttribute()
    {
        if ($this->isDendaPaid()) {
            return 0;
        }

        $paidAmount = $this->payments()
            ->where('payment_type', 'denda')
            ->whereIn('status', ['settlement', 'capture'])
            ->sum('amount');

        return max(0, ($this->denda ?? 0) - $paidAmount);
    }
}
