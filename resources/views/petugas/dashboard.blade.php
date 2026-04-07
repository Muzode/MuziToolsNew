@extends('layouts.app')
@section('content')
    <h3>Permintaan Peminjaman Masuk</h3>

    <div class="card mb-4">
        <div class="card-header bg-warning">Menunggu Persetujuan</div>
        <div class="card-body">
            <table class="table table-transparent">
                <thead>
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
                                <form action="{{ url('/petugas/approve/' . $loan->id) }}" method="POST" class="d￾inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Setujui</button>
                                </form>
                                <button class="btn btn-danger btn-sm">Tolak</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada permintaan baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <h3>Daftar Sedang Dipinjam (Belum Kembali)</h3>
    <div class="card">
        <div class="card-header bg-info text-white">Monitor Peminjaman</div>
        <div class="card-body">
            <table class="table table-transparent">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activeLoans as $active)
                        <tr>
                            <td>{{ $active->user->name }}</td>
                            <td>{{ $active->tool->nama_alat }}</td>
                            <td><span class="badge bg-primary">{{ $active->status }}</span></td>
                            <td>
                                <form action="{{ url('/petugas/return/' . $active->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">Proses Pengembalian</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <h3>Daftar Sudah Dikembalikan</h3>
    <div class="card">
        <div class="card-header bg-info text-white">Monitor Peminjaman</div>
        <div class="card-body">
            <table class="table table-transparent">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sudahDikembalikan as $sudah)
                        <tr>
                            <td>{{ $sudah->user->name }}</td>
                            <td>{{ $sudah->tool->nama_alat }}</td>
                            <td><span class="badge bg-primary">{{ $sudah->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
