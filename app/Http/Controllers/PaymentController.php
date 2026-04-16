<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Tampilkan halaman pembayaran denda
     */
    // PERBAIKI method showDendaPayment

    public function showDendaPayment(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $loan->updateDenda();

        if ($loan->unpaid_denda <= 0) {
            return redirect()->route('peminjam.riwayat')
                ->with('info', 'Tidak ada denda yang perlu dibayarkan.');
        }

        // 🔥 TAMBAHKAN: Expire payment yang sudah lewat
        Payment::where('loan_id', $loan->id)
            ->where('payment_type', 'denda')
            ->where('status', 'pending')
            ->where('expired_at', '<', now())
            ->update(['status' => 'expire']);

        // Cek payment pending yang valid
        $pendingPayment = Payment::where('loan_id', $loan->id)
            ->where('payment_type', 'denda')
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        return view('payments.denda', compact('loan', 'pendingPayment'));
    }
    /**
     * Create transaksi Midtrans
     */
    public function createTransaction(Request $request, Loan $loan)
    {
        // Validasi
        if ($loan->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $loan->updateDenda();

        if ($loan->unpaid_denda <= 0) {
            return response()->json(['error' => 'Tidak ada denda'], 422);
        }

        // 🔥 TAMBAHKAN: Hapus semua payment pending lama
        Payment::where('loan_id', $loan->id)
            ->where('payment_type', 'denda')
            ->where('status', 'pending')
            ->delete();

        // Generate order ID
        $orderId = 'DENDA-' . $loan->id . '-' . time() . '-' . Str::random(6);
        // Simpan payment record
        $payment = Payment::create([
            'loan_id' => $loan->id,
            'user_id' => auth()->id(),
            'amount' => $loan->unpaid_denda,
            'payment_type' => 'denda',
            'order_id' => $orderId,
            'status' => 'pending',
            'expired_at' => now()->addHours(24)
        ]);

        // Prepare customer details
        $user = auth()->user();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $loan->unpaid_denda,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'DENDA-' . $loan->id,
                    'price' => (int) $loan->unpaid_denda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan Peminjaman #' . $loan->id,
                ]
            ],
            'custom_field1' => 'loan_id_' . $loan->id,
            'custom_field2' => 'user_id_' . auth()->id(),
        ];

        try {
            // Get Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Update payment dengan snap token
            $payment->update([
                'payment_response' => [
                    'snap_token' => $snapToken,
                    'params' => $params
                ]
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'amount' => $loan->unpaid_denda
            ]);
        } catch (\Exception $e) {
            // Update payment status menjadi failure
            $payment->update([
                'status' => 'failure',
                'payment_response' => ['error' => $e->getMessage()]
            ]);

            return response()->json([
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle notification dari Midtrans (Webhook)
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $fraudStatus = $notification->fraud_status;
            $transactionId = $notification->transaction_id;

            // Cari payment berdasarkan order_id
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Update payment response
            $existingResponse = $payment->payment_response ?? [];
            $existingResponse['midtrans_notification'] = $notification->getResponse();
            $payment->payment_response = $existingResponse;
            $payment->transaction_id = $transactionId;
            $payment->payment_method = $paymentType;

            // Handle status berdasarkan transaction_status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $payment->status = 'capture';
                    $payment->paid_at = now();
                }
            } elseif ($transactionStatus == 'settlement') {
                $payment->status = 'settlement';
                $payment->paid_at = now();
            } elseif ($transactionStatus == 'pending') {
                $payment->status = 'pending';
            } elseif ($transactionStatus == 'deny') {
                $payment->status = 'deny';
            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                $payment->status = $transactionStatus;
            } elseif ($transactionStatus == 'failure') {
                $payment->status = 'failure';
            }

            $payment->save();

            // Log untuk debugging
            \Log::info('Midtrans Payment Update', [
                'order_id' => $orderId,
                'status' => $payment->status,
                'amount' => $payment->amount
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cek status pembayaran
     */
    public function checkStatus($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        return response()->json([
            'status' => $payment->status,
            'is_success' => $payment->isSuccess(),
            'is_pending' => $payment->isPending(),
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at
        ]);
    }

    /**
     * Halaman finish setelah payment
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return redirect()->route('peminjam.riwayat')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        if ($payment->isSuccess()) {
            return redirect()->route('peminjam.riwayat')
                ->with('success', 'Pembayaran denda berhasil! Terima kasih.');
        } else {
            return redirect()->route('peminjam.riwayat')
                ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
        }
    }
    public function cancelPayment($orderId)
    {
        try {
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Hanya bisa cancel jika status masih pending
            if ($payment->status != 'pending') {
                return response()->json(['error' => 'Payment cannot be cancelled'], 400);
            }

            // Update status menjadi cancel
            $payment->update([
                'status' => 'cancel',
                'payment_response' => array_merge($payment->payment_response ?? [], [
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id()
                ])
            ]);

            // Log aktivitas
            \Log::info('Payment cancelled', [
                'order_id' => $orderId,
                'loan_id' => $payment->loan_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            \Log::error('Cancel payment error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    public function manualUpdate(Request $request)
    {
        try {
            \Log::info('Manual update called', $request->all());

            $orderId = $request->order_id;
            $transactionId = $request->transaction_id;
            $paymentType = $request->payment_type;

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Order ID required'
                ], 400);
            }

            // Cari payment berdasarkan order_id
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'error' => 'Payment not found for order_id: ' . $orderId
                ], 404);
            }

            // Update status payment
            $payment->update([
                'status' => 'settlement',
                'transaction_id' => $transactionId,
                'payment_method' => $paymentType,
                'paid_at' => now(),
                'payment_response' => array_merge($payment->payment_response ?? [], [
                    'manual_update' => true,
                    'updated_at' => now(),
                    'result' => $request->all()
                ])
            ]);

            \Log::info('Payment updated manually', [
                'payment_id' => $payment->id,
                'order_id' => $orderId,
                'status' => $payment->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            \Log::error('Manual update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
