@extends('layouts.app')
@section('content')
<div class="">
    <div class="m-5 text-center">
        <h3 class="fs-1">MUZITOOLS</h3>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card mb-3 h-100">
                <div class="card-header">Total Pengguna</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalUser }}</h2>
                    <p class="card-text">User Terdaftar</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('users.index') }}" class="text-dark text-decoration-none small">Lihat Detail</a>
                    <span class="small">&rarr;</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark mb-3 h-100">
                <div class="card-header">Data Alat</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalAlat }} <span class="fs-6">(Stok: {{ $totalStok }})</span></h2>
                    <p class="card-text">Jenis Alat Tersedia</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('tools.index') }}" class="text-dark text-decoration-none small">Lihat Detail</a>
                    <span class="small">&rarr;</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark mb-3 h-100">
                <div class="card-header">Kategori</div>
                <div class="card-body ">
                    <h2 class="card-title">{{ $totalKategori }}</h2>
                    <p class="card-text">Kategori Alat</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('categories.index') }}" class="text-dark text-decoration-none small">Lihat Detail</a>
                    <span class="small ">&rarr;</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-danger mb-3 h-100">
                <div class="card-header">Sedang Dipinjam</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $sedangDipinjam }}</h2>
                    <p class="card-text">Transaksi Aktif</p>

                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.loans.index') }}" class="text-white text-decoration-none small">Pantau</a>
                    <span class="small">&rarr;</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3 h-100">
                <div class="card-header">Sudah Dikembalikan</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $sudahDikembalikan }}</h2>
                    <p class="card-text">Transaksi Selesai</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.returns.index') }}" class="text-white text-decoration-none small">Pantau</a>
                    <span class="small">&rarr;</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light fw-bold">
                    Aktivitas Sistem Terakhir
                </div>
                <div class="card-body p-0">
                    <table class="table table-transparent table-hover mb-0">
                        <thead class="">
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td class="small text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $log->user->name }}</span>
                                        <br>
                                        <span class="badge bg-secondary"
                                            style="font-size: 0.7em">{{ ucfirst($log->user->role) }}</span>
                                    </td>
                                    <td>{{ $log->action }}</td>
                                    <td class="text-muted small">{{ Str::limit($log->description, 50) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">Belum ada aktivitas tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-light">Lihat Semua Log</a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
