<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MuziTools - Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="text-dark m-0">
    <div class="fullscreen-gradient">
        <nav class="navbar navbar-expand-lg navbar-light shadow mb-4 sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">MuziTools</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (auth()->user()->role == 'admin')
                                <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kategori</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('tools.index') }}">Alat</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">User</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.loans.index') }}">Peminjaman</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.returns.index') }}">Pengembalian</a></li>
                            @elseif(auth()->user()->role == 'petugas')
                                <li class="nav-item"><a class="nav-link" href="/petugas/dashboard">Validasi Peminjaman</a>
                                </li>
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
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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
    </div>
<!-- Karakter Melayang - Versi dengan pengecekan -->
<div class="floating-character">
    @if(file_exists(public_path('images/character.png')))
        <img src="{{ asset('images/character.png') }}" alt="Asisten" id="char">
    @else
        <!-- Gambar default dari internet -->
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712109.png" alt="Asisten" id="char">
    @endif
    <div class="bubble-chat" id="bubble">
        <span class="close-bubble" id="close">×</span>
        <div id="msg">👋 Halo! Ada yang bisa saya bantu?</div>
    </div>
</div>
</div>

<script>
// JavaScript pendek untuk pemula
let pesan = [
    "👋 Halo! Selamat datang!",
    "📋 Jangan lupa kembalikan alat tepat waktu!",
    "⚠️ Telat kena denda Rp 5.000/hari",
    "💡 Kembalikan sesuai jadwal ya!"
];
let urutan = 0;
let timer;

// Ambil elemen
let karakter = document.getElementById('char');
let bubble = document.getElementById('bubble');
let tutup = document.getElementById('close');
let tempatPesan = document.getElementById('msg');

// Fungsi tampilkan bubble
function tampilkanPesan(teks) {
    tempatPesan.innerHTML = teks;
    bubble.classList.add('show');
    
    // Hapus timer lama
    if (timer) clearTimeout(timer);
    
    // Auto tutup setelah 4 detik
    timer = setTimeout(() => {
        bubble.classList.remove('show');
    }, 4000);
}

// Saat karakter diklik
karakter.onclick = function() {
    tampilkanPesan(pesan[urutan]);
    urutan = (urutan + 1) % pesan.length;
    
    // Efek klik
    this.style.transform = 'scale(0.9)';
    setTimeout(() => {
        this.style.transform = 'scale(1)';
    }, 150);
}

// Tombol tutup
tutup.onclick = function() {
    bubble.classList.remove('show');
    if (timer) clearTimeout(timer);
}

// Pesan sambutan saat buka halaman
setTimeout(() => {
    tampilkanPesan("👋 Selamat datang!");
}, 1500);
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
