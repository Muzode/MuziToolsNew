@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="card mb-4 shadow-sm border-0 rounded-4"> 
        <h3 class="pt-4 pb-2">Permintaan Peminjaman Masuk</h3>
        <div class="card">
            <div class="card-header bg-warning text-dark">Menunggu Persetujuan</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>{{ $loan->user->name }}</td>
                                    <td>{{ $loan->tool->nama_alat }}</td>
                                    <td>{{ $loan->tanggal_pinjam }}</td>
                                    <td>{{ $loan->tanggal_kembali_rencana }}</td>
                                    <td>
                                        <form action="{{ url('/petugas/approve/'.$loan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Setujui</button>
                                        </form>
                                        <button class="btn btn-danger btn-sm">Tolak</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">Tidak ada permintaan baru</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <h3 class="pt-4 pb-2">Daftar belum kembali</h3>
        <div class="card">
            <div class="card-header bg-info text-dark">Monitor Peminjaman</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Status</th>
                                <th>Rencana Kembali</th>
                                <th>Tanggal Kembali Aktual</th>
                                <th>Denda (Rp)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeLoans as $active)
                                <tr id="loan-row-{{ $active->id }}">
                                    <td>{{ $active->user->name }}</td>
                                    <td>{{ $active->tool->nama_alat }}</td>
                                    <td><span class="badge bg-primary">{{ $active->status }}</span></td>
                                    <td class="tgl-rencana" data-tgl="{{ $active->tanggal_kembali_rencana->format('Y-m-d') }}">
                                        {{ $active->tanggal_kembali_rencana->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        <input type="date" name="tanggal_kembali_aktual" class="form-control form-control-sm tgl-aktual" 
                                               data-id="{{ $active->id }}"
                                               value="{{ date('Y-m-d') }}" required>
                                    </td>
                                    <td class="denda-display" id="denda-{{ $active->id }}">
                                        <span class="text-muted">Rp 0</span>
                                    </td>
                                    <td>
                                        <form action="{{ url('/petugas/return/'.$active->id) }}" method="POST" class="return-form">
                                            @csrf
                                            <input type="hidden" name="tanggal_kembali_aktual" class="hidden-tgl" value="">
                                            <button type="submit" class="btn btn-primary btn-sm">Proses Kembali</button>
                                        </form>
                                    </td> 
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 shadow-sm border-0 rounded-4">
        <h3 class="pt-4 pb-2">Daftar Sudah Dikembalikan</h3>
        <div class="card">
            <div class="card-header bg-success text-dark">Monitor Peminjaman</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-transparent table-bordered table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Status</th>
                                <th>Tanggal Kembali Aktual</th>
                                <th>Denda (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sudahDikembalikan as $sudah)
                                <tr>
                                    <td>{{ $sudah->user->name }}</td>
                                    <td>{{ $sudah->tool->nama_alat }}</td>
                                    <td><span class="badge bg-success">Sudah Kembali</span></td>
                                    <td>{{ $sudah->tanggal_kembali_aktual ? $sudah->tanggal_kembali_aktual->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        @if($sudah->denda > 0)
                                            <span class=" fw-bold">Rp {{ number_format($sudah->denda, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">Rp 0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7  " class="text-center text-muted">Tidak ada data</td>
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