@extends('layouts.app')
@section('content')
    <h3 class="text-dark">Daftar Alat Tersedia</h3>
        <div class="mb-3">
        <form action="{{ url('/peminjam/dashboard') }}" method="GET" class="d-flex gap-2" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Cari alat atau kategori..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
    </div>

    <div class="row mt-4">
        @foreach ($tools as $tool)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tool->nama_alat }}</h5>
                        <div class="text-center mb-3">
                            @if ($tool->gambar)
                                <img src="{{ asset('storage/' . $tool->gambar) }}" alt="img" class="img￾thumbnail"
                                    style="width: 150px; height: 150px;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light rounded mx-auto"
                                    style="width: 150px; height: 150px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <span class="badge bg-secondary mb-2">{{ $tool->category->nama_kategori }}</span>
                        <p class="card-text">{{ $tool->deskripsi }}</p>
                        <p class="fw-bold">Sisa Stok: {{ $tool->stok }}</p>
                        @if ($tool->stok > 0)
                            <form action="{{ url('/peminjam/ajukan') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tool_id" value="{{ $tool->id }}">
                                <div class="mb-2">
                                    <label class="small">Tgl Rencana Kembali</label>
                                    <input type="date" name="tanggal_kembali" class="form-control form-control-sm"
                                        required min="{{ date('Y-m-d') }}">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Pinjam Alat</button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
