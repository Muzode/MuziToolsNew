@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between text-dark align-items-center mb-4">
        <h3>Kelola Data Alat</h3>
        <a href="{{ route('tools.create') }}" class="btn btn-primary">
            + Tambah Alat Baru
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-transparent table-striped align-middle">
                    <thead class="">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Gambar</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tools as $key => $tool)
                            <tr>
                                <td>{{ $tools->firstItem() + $key }}</td>
                                <td>
                                    @if ($tool->gambar)
                                        <img src="{{ asset('storage/' . $tool->gambar) }}" alt="img"
                                            class="img￾thumbnail" style="height: 60px;">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $tool->nama_alat }}</strong>
                                    <div class="small text-muted text-truncate" style="max-width: 200px;">

                                        {{ $tool->deskripsi }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $tool->category->nama_kategori }}
                                    </span>
                                </td>
                                <td>{{ $tool->stok }} unit</td>
                                <td>
                                    <a href="{{ route('tools.edit', $tool->id) }}" class="btn btn-warning btnsm">
                                        Edit
                                    </a>
                                    <form action="{{ route('tools.destroy', $tool->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus alat ini? Data peminjaman terkait mungkin akan error.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Belum ada data alat. Silakan tambah data baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tools->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
