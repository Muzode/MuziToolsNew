@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Kelola Data Peminjaman (Admin)</h3>
        <a href="{{ route('admin.loans.create') }}" class="btn btn-primary">+ Tambah Peminjaman Manual</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-transparent table-striped">
                <thead class="">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $key => $loan)
                        <tr>
                            <td>{{ $loans->firstItem() + $key }}</td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->tool->nama_alat }}</td>
                            <td>
                                {{ $loan->tanggal_pinjam }} <br>
                                <small class="text-muted">Kembali: {{ $loan->tanggal_kembali_rencana }}</small>
                            </td>
                            <td>
                                @if ($loan->status == 'pending')
                                    <span class="badge bg-warning textdark">Pending</span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="badge bg-primary">Sedang Dipinjam</span>
                                @elseif($loan->status == 'kembali')
                                    <span class="badge bg-success">Sudah Kembali</span>
                                @elseif($loan->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.loans.edit', $loan->id) }}"
                                    class="btn btn-sm btn-info text-white">Edit</a>
                                <form action="{{ route('admin.loans.destroy', $loan->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $loans->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection
