{{-- resources/views/payments/denda.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Pembayaran Denda
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Denda -->
                        <div class="alert alert-danger mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Informasi Denda</strong><br>
                                    <small>Anda terlambat mengembalikan alat peminjaman</small>
                                </div>
                                <div class="text-end">
                                    <div class="small">Total Denda</div>
                                    <div class="h4 mb-0 fw-bolder text-light">
                                        Rp {{ number_format($loan->unpaid_denda, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Peminjaman -->
                        <div class="card mb-4 bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">Detail Peminjaman</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%">Nama Alat</td>
                                                <td><strong>{{ $loan->tool->nama_alat ?? '-' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah</td>
                                                <td>{{ $loan->quantity }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Pinjam</td>
                                                <td>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%">Rencana Kembali</td>
                                                <td>{{ $loan->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tgl Kembali Aktual</td>
                                                <td class="text-danger">
                                                    <strong>{{ $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Denda per Hari</td>
                                                <td>Rp {{ number_format($loan->denda_per_hari ?? 5000, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Payment Alert dengan Tombol Cancel -->
                        @if ($pendingPayment && $pendingPayment->isPending())
                            <div class="alert alert-warning">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <i class="fas fa-hourglass-half me-2"></i>
                                        <strong>Pembayaran sedang diproses!</strong>
                                        <br>
                                        <small>Order ID: {{ $pendingPayment->order_id }}</small>
                                    </div>
                                    <div class="mt-2 mt-sm-0">
                                        <button class="btn btn-sm btn-secondary me-2"
                                            onclick="checkPaymentStatus('{{ $pendingPayment->order_id }}')">
                                            <i class="fas fa-sync-alt me-1"></i>Cek Status
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="cancelPayment('{{ $pendingPayment->order_id }}')">
                                            <i class="fas fa-times me-1"></i>Batalkan Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tombol Bayar -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger btn-lg" id="payButton"
                                {{ $pendingPayment ? 'disabled' : '' }}>
                                <i class="fas fa-credit-card me-2"></i>
                                Bayar Denda Sekarang
                            </button>
                        </div>

                        <!-- Informasi Penting -->
                        <div class="alert alert-secondary mt-4 small">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi Penting:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Pembayaran menggunakan Midtrans (Bank Transfer, E-Wallet, QRIS, Credit Card)</li>
                                <li>Setelah pembayaran berhasil, status akan otomatis terupdate</li>
                                <li>Jika pembayaran pending, Anda bisa membatalkan dan memulai pembayaran baru</li>
                                <li>Jika ada kendala, hubungi admin</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        let snapToken = null;

        // Event listener untuk tombol bayar
        document.getElementById('payButton').addEventListener('click', async function() {
            const payButton = this;
            const originalText = payButton.innerHTML;

            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            try {
                const response = await fetch('{{ route('payments.create-transaction', $loan) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success && data.snap_token) {
                    snapToken = data.snap_token;

                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            // 🔥 KIRIM KONFIRMASI MANUAL KE SERVER
                            fetch('{{ route('payments.manual-update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id,
                                        transaction_id: result.transaction_id,
                                        payment_type: result.payment_type
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href =
                                            '{{ route('payments.finish') }}?order_id=' + result
                                            .order_id;
                                    } else {
                                        alert(
                                            'Pembayaran berhasil tapi gagal update status. Silakan hubungi admin.');
                                        window.location.href =
                                            '{{ route('peminjam.riwayat') }}';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    window.location.href =
                                        '{{ route('payments.finish') }}?order_id=' + result
                                        .order_id;
                                });
                        },
                        onPending: function(result) {
                            alert('Pembayaran pending. Silakan selesaikan pembayaran.');
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert('Terjadi kesalahan dalam pembayaran.');
                            window.location.reload();
                        },
                        onClose: function() {
                            payButton.disabled = false;
                            payButton.innerHTML = originalText;
                            alert('Jendela pembayaran ditutup. Silakan lanjutkan nanti.');
                        }
                    });
                } else {
                    throw new Error(data.error || 'Gagal membuat transaksi');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                payButton.disabled = false;
                payButton.innerHTML = originalText;
            }
        });

        // Fungsi cek status payment
        async function checkPaymentStatus(orderId) {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memeriksa...';

            try {
                const response = await fetch(`/payments/check/${orderId}`);
                const data = await response.json();

                if (data.is_success) {
                    window.location.href = '{{ route('payments.finish') }}?order_id=' + orderId;
                } else if (data.is_pending) {
                    alert('Pembayaran masih pending. Silakan selesaikan pembayaran.');
                    button.disabled = false;
                    button.innerHTML = originalText;
                } else {
                    alert('Status pembayaran: ' + data.status);
                    window.location.reload();
                }
            } catch (error) {
                alert('Gagal mengecek status');
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Fungsi cancel payment
        async function cancelPayment(orderId) {
            if (!confirm(
                    'Yakin ingin membatalkan pembayaran ini?\n\nSetelah dibatalkan, Anda bisa membuat pembayaran baru.'
                    )) {
                return;
            }

            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Membatalkan...';

            try {
                const response = await fetch(`/payments/cancel/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Pembayaran berhasil dibatalkan!');
                    window.location.reload();
                } else {
                    throw new Error(data.error || 'Gagal membatalkan pembayaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Auto check status jika ada pending payment
        @if ($pendingPayment && $pendingPayment->isPending())
            let checkInterval = setInterval(() => {
                checkPaymentStatus('{{ $pendingPayment->order_id }}');
            }, 10000);

            window.addEventListener('beforeunload', function() {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            });
        @endif
    </script>
@endsection
