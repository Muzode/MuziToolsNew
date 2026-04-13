@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-bold">Edit Data Pengembalian</div>
                <div class="card-body">
                    <form action="{{ route('admin.returns.update', $loan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Peminjam</label>
                            <input type="text" class="form-control" value="{{ $loan->user->name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Alat</label>
                            <input type="text" class="form-control" value="{{ $loan->tool->nama_alat }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Kembali Aktual</label>
                            <input type="date" name="tanggal_kembali_aktual" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                            <small class="text-muted">Ubah tanggal ini jika admin salah input waktu pengembalian.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" name="gambar_kondisi" class="form-control @error('gambar') is-invalid @enderror"
                                accept="image/*">
                            @if ($loan->gambar_kondisi)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Gambar Saat Ini:</small>
                                    <img src="{{ asset('storage/' . $loan->gambar_kondisi) }}" alt="Current Image"
                                        class="img-thumbnail" style="height: 80px;">
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label>Keterangan Kondisi</label>
                            <textarea name="keterangan_kondisi" class="form-control form-control-sm" placeholder="Kondisi alat..." maxlength="500"
                                rows="2" style="min-width: 120px;">{{ $loan->keterangan_kondisi }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between">

                            <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
