@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Data Pengembalian Alat</h3>
        <a href="{{ route('admin.returns.create') }}" class="btn btn-success">
            + Proses Pengembalian Baru
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-transparent table-striped">
                <thead class="">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali (Aktual)</th>
                        <th>Denda</th>
                        <th>Keterangan Kondisi</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $key => $r)
                        
                            <td>{{ $returns->firstItem() + $key }}</td>
                            <td>{{ $r->user->name }}</td>
                            <td>{{ $r->tool->nama_alat }}</td>
                            <td>{{ $r->tanggal_pinjam }}</td>
                            <td>
                                {{ $r->tanggal_kembali_aktual }}
                                @if ($r->tanggal_kembali_aktual > $r->tanggal_kembali_rencana)
                                    <span class="badge bg-danger">Telat</span>
                                @else
                                    <span class="badge bg-success">Tepat Waktu</span>
                                @endif
                            </td>
                            <td>{{ number_format($r->denda, 0, ',', '.') }}</td>
                            <td>
                                <div>{{ $r->keterangan_kondisi ?? '-' }}</div>
                                <div>
                                    @if ($r->gambar_kondisi)
                                        <a href="{{ asset('storage/' . $r->gambar_kondisi) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-image"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted"><i class="fas fa-image"></i>none</span>
                                    @endif
                                </div>
                            <td>{{ $r->petugas ? $r->petugas->name : 'Admin' }}</td>
                            <td>
                                <a href="{{ route('admin.returns.edit', $r->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.returns.destroy', $r->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus riwayat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data pengembalian.</td>
                            </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $returns->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection
