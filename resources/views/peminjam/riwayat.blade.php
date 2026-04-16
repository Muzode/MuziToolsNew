@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h3 class="text-dark mb-3">Riwayat Peminjaman Saya</h3>
    
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-transparent">
                    <thead>
                        <tr>
                            <th>Alat</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Aksi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td>
                                {{ $loan->tool->nama_alat ?? 'Alat tidak ditemukan' }}
                                <br>
                                <small class="badge bg-primary">Jumlah: {{ $loan->quantity }}</small>
                            </td>
                            <td>{{ $loan->tanggal_pinjam ? $loan->tanggal_pinjam->format('d-m-Y') : '-' }}</td>
                            <td>{{ $loan->tanggal_kembali_rencana ? $loan->tanggal_kembali_rencana->format('d-m-Y') : '-' }}</td>
                            <td>
                                @if ($loan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="badge bg-primary">Sedang Dipinjam</span>
                                @elseif($loan->status == 'diajukan')
                                    <span class="badge bg-info text-dark">Pengembalian Diajukan</span>
                                @elseif($loan->status == 'kembali')
                                    <span class="badge bg-success">Sudah Dikembalikan</span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if (isset($loan->denda) && $loan->denda > 0)
                                    <span class="text-danger fw-bold">
                                        Rp {{ number_format($loan->denda, 0, ',', '.') }}
                                    </span>
                                    @if (method_exists($loan, 'isDendaPaid') && !$loan->isDendaPaid())
                                        <br>
                                        <small class="badge bg-warning">Belum dibayar</small>
                                    @elseif(method_exists($loan, 'isDendaPaid') && $loan->isDendaPaid())
                                        <br>
                                        <small class="badge bg-success">Lunas</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{-- Tombol Bayar Denda --}}
                                @if ($loan->status == 'kembali' && isset($loan->denda) && $loan->denda > 0)
                                    @if (method_exists($loan, 'isDendaPaid') && !$loan->isDendaPaid())
                                        <a href="{{ route('payments.denda', $loan) }}" class="btn btn-danger btn-sm mb-2">
                                            <i class="fas fa-credit-card me-1"></i>Bayar Denda
                                        </a>
                                    @endif
                                @endif

                                {{-- Tombol Ajukan Pengembalian --}}
                                @if ($loan->status == 'disetujui')
                                    <form action="{{ route('peminjam.request-return', $loan->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin mengajukan pengembalian alat ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-undo-alt"></i> Ajukan Pengembalian
                                        </button>
                                    </form>
                                @elseif($loan->status == 'diajukan')
                                    <span class="badge bg-info">Menunggu Konfirmasi</span>
                                @elseif($loan->status == 'kembali')
                                    <span class="badge bg-success">✓ Selesai</span>
                                @endif
                            </td>
                            <td>
                                @if ($loan->status == 'disetujui')
                                    <small class="text-muted">Harap kembalikan ke petugas sebelum tanggal rencana.</small>
                                @elseif($loan->status == 'kembali')
                                    <small class="text-success">
                                        Diterima {{ $loan->tanggal_kembali_aktual ? $loan->tanggal_kembali_aktual->format('d-m-Y') : '-' }}
                                    </small>
                                    @if(isset($loan->keterangan_kondisi) && $loan->keterangan_kondisi)
                                        <br>
                                        <small class="text-muted">Kondisi: {{ $loan->keterangan_kondisi }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada riwayat peminjaman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection