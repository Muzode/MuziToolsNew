@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Proses Pengembalian Alat</h3>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">Kembali ke Riwayat</a>
    </div>
    <div class="alert alert-info"> Silakan pilih data peminjaman di bawah ini untuk diproses pengembaliannya.
    </div>
    <div class="card">
        <div class="card-header bg-primary text-white">Daftar Alat Sedang Dipinjam</div>
        <div class="card-body">

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeLoans as $loan)
                        <tr>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->tool->nama_alat }}</td>
                            <td>{{ $loan->tanggal_pinjam }}</td>
                            <td>
                                {{ $loan->tanggal_kembali_rencana }}
                                @if (now() > $loan->tanggal_kembali_rencana)
                                    <span class="badge bg-danger">Lewat Jatuh Tempo</span>
                                @endif
                            </td>
                            <td><span class="badge bg-primary">Sedang Dipinjam</span></td>
                            <td>
                                <form action="{{ route('admin.returns.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                    <button type="submit" class="btn btn-success btn-sm"
                                        onclick="return confirm('Konfirmasi: Barang sudah diterima kembali dan kondisi baik?')">
                                        Proses Kembali
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada alat yang sedang dipinjam saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
