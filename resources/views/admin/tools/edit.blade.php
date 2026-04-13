@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header fw-bold">Edit Data Alat</div>
                    <div class="card-body">
                        <form action="{{ route('tools.update', $tool->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <div class="mb-3">
                                <label class="form-label">Nama Alat</label>
                                <input type="text" name="nama_alat"
                                    class="form-control @error('nama_alat') is-invalid @enderror"
                                    value="{{ old('nama_alat', $tool->nama_alat) }}" required>
                                @error('nama_alat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select @error('category_id') is￾invalid @enderror"
                                    required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $tool->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Stok</label>
                                    <input type="number" name="stok"
                                        class="form-control @error('stok') is-invalid @enderror"
                                        value="{{ old('stok', $tool->stok) }}" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ganti Gambar (Opsional)</label>
                                    <input type="file" name="gambar"
                                        class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                                    @if ($tool->gambar)
                                        <div class="mt-2">
                                            <small class="text-muted d-block mb-1">Gambar Saat Ini:</small>
                                            <img src="{{ asset('storage/' . $tool->gambar) }}" alt="Current Image"
                                                class="img-thumbnail" style="height: 80px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $tool->deskripsi) }}</textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tools.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-success">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
