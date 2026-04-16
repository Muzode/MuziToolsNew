<?php
// database/migrations/2024_04_15_123804_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['denda', 'deposit', 'other'])->default('denda');
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', ['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure'])->default('pending');
            $table->json('payment_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            
            // Index untuk mempercepat query
            $table->index(['loan_id', 'status']);
            $table->index('order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};