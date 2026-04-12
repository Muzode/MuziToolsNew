@extends('layouts.app')
@section('content')
    <h3>Riwayat Peminjaman Saya</h3>
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-transparent">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->tool->nama_alat }}</td>
                            <td>{{ $loan->tanggal_pinjam }}</td>
                            <td>{{ $loan->tanggal_kembali_rencana }}</td>
                            <td>
                                @if ($loan->status == 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="badge bg-primary">Sedang Dipinjam</span>
                                @elseif($loan->status == 'kembali')
                                    <span class="badge bg-success">Sudah Dikembalikan</span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                Rp.{{ number_format($loan->denda, 0, ',', '.') }}
                                @empty($loan->denda)
                                    <span class="text-muted">-</span>
                                @endempty
                            </td>
                            <td>
                                @if ($loan->status == 'disetujui')
                                    <small class="text-muted">Harap kembalikan ke petugas sebelum tanggal rencana.</small>
                                @elseif($loan->status == 'kembali')
                                    <small class="text-success">Diterima tanggal {{ $loan->tanggal_kembali_aktual }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Belum ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
