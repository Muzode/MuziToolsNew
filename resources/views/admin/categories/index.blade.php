@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between text-dark align-items-center mb-4">
                <h3>Kelola Kategori Alat</h3>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    + Tambah Kategori
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-transparent table-striped">
                        <thead class="">
                            <tr>
                                <th width="10%">No</th>
                                <th>Nama Kategori</th>
                                <th width="20%">Jumlah Alat</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $key => $cat)
                                <tr>
                                    <td>{{ $categories->firstItem() + $key }}</td>
                                    <td>{{ $cat->nama_kategori }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $cat->tools_count }} Item
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('categories.edit', $cat->id) }}"
                                            class="btn btn-warning  btn-sm">Edit</a>
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus kategori ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada kategori.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
