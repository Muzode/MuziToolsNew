@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <div class="d-flex justify-content-between align-items-center mb-3 p-3">
            <h3 class="pt-3">Proses Pengembalian Alat</h3>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">Kembali ke Riwayat</a>
        </div>
        
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="alert alert-info mx-3">
            <i class="fas fa-info-circle"></i> Silakan pilih data peminjaman di bawah ini untuk diproses pengembaliannya.
            Denda akan dihitung otomatis Rp 5.000/hari jika melebihi tanggal rencana.
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-clipboard-list"></i> Daftar Alat Sedang Dipinjam
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Status</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Tanggal Kembali Aktual</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeLoans as $index => $active)
                                <tr id="loan-row-{{ $active->id }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $active->user->name }}</td>
                                    <td>{{ $active->tool->nama_alat }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $active->status }}</span>
                                    </td>
                                    <td>{{ $active->tanggal_pinjam->format('d-m-Y') }}</td>
                                    <td class="tgl-rencana" data-tgl="{{ $active->tanggal_kembali_rencana->format('Y-m-d') }}">
                                        {{ $active->tanggal_kembali_rencana->format('d-m-Y') }}
                                        @if(now()->gt($active->tanggal_kembali_rencana))
                                            <span class="badge bg-danger ms-1">Telat!</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="date" 
                                               class="form-control form-control-sm tgl-aktual" 
                                               data-id="{{ $active->id }}"
                                               data-rencana="{{ $active->tanggal_kembali_rencana->format('Y-m-d') }}"
                                               value="{{ date('Y-m-d') }}" 
                                               style="min-width: 140px;">
                                    </td>
                                    <td class="denda-display" id="denda-{{ $active->id }}">
                                        <span class="text-muted">Rp 0</span>
                                    </td>
                                    <td>
<form action="{{ route('admin.returns.store') }}" method="POST" class="return-form">
    @csrf
    <input type="hidden" name="loan_id" value="{{ $active->id }}">
    <input type="hidden" name="tanggal_kembali_aktual" class="hidden-tgl" value="">
    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-check-circle"></i> Proses Kembali
    </button>
</form>                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Tidak ada data peminjaman aktif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menghitung denda
        function hitungDenda(tglRencana, tglAktual) {
            if (!tglRencana || !tglAktual) return 0;
            const rencana = new Date(tglRencana);
            const aktual = new Date(tglAktual);
            
            // Reset jam ke 00:00:00 untuk perbandingan hari
            rencana.setHours(0, 0, 0, 0);
            aktual.setHours(0, 0, 0, 0);
            
            if (aktual <= rencana) return 0;
            
            // Hitung selisih hari
            const diffTime = aktual - rencana;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays * 5000;
        }
        
        // Fungsi untuk memperbarui tampilan denda
        function updateDenda(inputElement) {
            const row = inputElement.closest('tr');
            const tglRencanaElem = row.querySelector('.tgl-rencana');
            const tglAktualValue = inputElement.value;
            const loanId = inputElement.getAttribute('data-id');
            
            if (tglRencanaElem && tglAktualValue) {
                const tglRencana = tglRencanaElem.getAttribute('data-tgl');
                const denda = hitungDenda(tglRencana, tglAktualValue);
                const dendaDisplay = document.getElementById('denda-' + loanId);
                if (dendaDisplay) {
                    if (denda > 0) {
                        dendaDisplay.innerHTML = '<span class=" fw-bold">Rp ' + denda.toLocaleString('id-ID') + '</span>';
                    } else {
                        dendaDisplay.innerHTML = '<span class="text-muted">Rp 0</span>';
                    }
                }
                // Update hidden input pada form dengan nilai tanggal aktual
                const form = row.querySelector('.return-form');
                const hiddenInput = form.querySelector('.hidden-tgl');
                if (hiddenInput) {
                    hiddenInput.value = tglAktualValue;
                }
            }
        }
        
        // event listener untuk semua input tanggal aktual
        const inputs = document.querySelectorAll('.tgl-aktual');
        inputs.forEach(input => {
            // Hitung denda saat halaman pertama kali dimuat
            updateDenda(input);
            
            // Hitung denda saat tanggal berubah
            input.addEventListener('change', function() {
                updateDenda(this);
            });
        });
    });
</script>

@endsection