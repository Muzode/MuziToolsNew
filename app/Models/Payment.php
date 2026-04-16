<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments'; // Pastikan nama tabel sesuai

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'payment_type',
        'order_id',
        'transaction_id',
        'payment_method',
        'status',
        'payment_response',
        'paid_at',
        'expired_at'
    ];

    protected $casts = [
        'payment_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccess()
    {
        return in_array($this->status, ['settlement', 'capture']);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isExpired()
    {
        return $this->status === 'expire';
    }
}
