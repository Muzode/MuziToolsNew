<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Peminjaman</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if (auth()->user()->role == 'admin')
                            <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kelola
                                    Kategori</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('tools.index') }}">Kelola Alat</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Kelola User</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.loans.index') }}">Kelola
                                    Peminjaman</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.returns.index') }}">Kelola
                                    Pengembalian</a></li>
                        @elseif(auth()->user()->role == 'petugas')
                            <li class="nav-item"><a class="nav-link" href="/petugas/dashboard">Validasi Peminjaman</a></li>
                            <li class="nav-item"><a class="nav-link" href="/petugas/laporan">Laporan</a></li>
                        @elseif(auth()->user()->role == 'peminjam')
                            <li class="nav-item"><a class="nav-link" href="/peminjam/dashboard">Daftar Alat</a></li>
                            <li class="nav-item"><a class="nav-link" href="/peminjam/riwayat">Riwayat Saya</a></li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs￾toggle="dropdown">
                                {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
