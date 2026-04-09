<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal_pinjam'        => 'datetime',
        'tanggal_kembali_rencana'=> 'datetime',
        'tanggal_kembali_aktual' => 'datetime',
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

    /**
     * Hitung denda keterlambatan.
     * Denda Rp 5.000 per hari jika tanggal_kembali_aktual > tanggal_kembali_rencana.
     */
    public function calculateDenda()
    {
        // Jika tanggal aktual belum diisi, belum bisa hitung denda
        if (!$this->tanggal_kembali_aktual || !$this->tanggal_kembali_rencana) {
            return 0;
        }

        // Jika tanggal aktual <= rencana, tidak telat
        if ($this->tanggal_kembali_aktual->lte($this->tanggal_kembali_rencana)) {
            return 0;
        }

        // Hitung selisih jam (pembulatan ke atas, atau tepat per jam)
        $diffHours = $this->tanggal_kembali_rencana->diffInHours($this->tanggal_kembali_aktual);
        $hariTelat = (int) ceil($diffHours / 24);

        // diffInDays sudah positif karena aktual > rencana
        return $hariTelat * 5000;
    }
}