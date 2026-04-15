<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOOLZ - Aplikasi Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

    </style>
</head>

<body class="text-dark m-0">
    <!-- Konten navbar dan lainnya sama seperti kode Anda -->
    <div class="fullscreen-gradient">
        <nav class="navbar navbar-expand-lg navbar-light shadow mb-4 sticky-top">
            <div class="container">
                <a class="navbar-brand" href="#">TOOLZ</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (auth()->user()->role == 'admin')
                                <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kategori</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('tools.index') }}">Alat</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">User</a>
                                </li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.loans.index') }}">Peminjaman</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.returns.index') }}">Pengembalian</a></li>
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

    <!-- Karakter Melayang - Bisa Dipindahkan -->
    <div class="floating-character" id="floatingChar">
        <img src="{{ asset('images/character.png') }}" alt="Asisten" id="char">
        <div class="bubble-chat" id="bubble">
            <span class="close-bubble" id="close">×</span>
            <div id="msg">👋 Halo! Ada yang bisa saya bantu?</div>
        </div>
    </div>

    <script>
        // ========== FITUR DRAG & DROP (Bisa dipindahkan) ==========
        let floatingChar = document.getElementById('floatingChar');
        let isDragging = false;
        let startX, startY, startLeft, startTop;

        // Posisi awal dari CSS
        let posLeft = null;
        let posTop = null;

        // Fungsi untuk mendapatkan posisi elemen
        function getElementPosition() {
            let rect = floatingChar.getBoundingClientRect();
            return {
                left: rect.left,
                top: rect.top
            };
        }

        // Event mouse/touch untuk mulai drag
        function startDrag(e) {
            e.preventDefault();
            isDragging = true;

            // Ambil posisi awal kursor/touch
            if (e.type === 'mousedown') {
                startX = e.clientX;
                startY = e.clientY;
            } else if (e.type === 'touchstart') {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }

            // Ambil posisi awal elemen
            let pos = getElementPosition();
            startLeft = pos.left;
            startTop = pos.top;

            // Ubah style jadi fixed dengan posisi absolute relatif ke viewport
            floatingChar.style.cursor = 'grabbing';
        }

        // Event untuk drag
        function onDrag(e) {
            if (!isDragging) return;
            e.preventDefault();

            let currentX, currentY;
            if (e.type === 'mousemove') {
                currentX = e.clientX;
                currentY = e.clientY;
            } else if (e.type === 'touchmove') {
                currentX = e.touches[0].clientX;
                currentY = e.touches[0].clientY;
            }

            // Hitung perpindahan
            let deltaX = currentX - startX;
            let deltaY = currentY - startY;

            // Posisi baru
            let newLeft = startLeft + deltaX;
            let newTop = startTop + deltaY;

            // Batasi agar tidak keluar layar
            let maxLeft = window.innerWidth - floatingChar.offsetWidth;
            let maxTop = window.innerHeight - floatingChar.offsetHeight;

            newLeft = Math.max(0, Math.min(newLeft, maxLeft));
            newTop = Math.max(0, Math.min(newTop, maxTop));

            // Terapkan posisi baru
            floatingChar.style.left = newLeft + 'px';
            floatingChar.style.top = newTop + 'px';
            floatingChar.style.right = 'auto';
            floatingChar.style.bottom = 'auto';
        }

        // Event stop drag
        function stopDrag() {
            isDragging = false;
            floatingChar.style.cursor = 'grab';
        }

        // Pasang event listener untuk mouse
        floatingChar.addEventListener('mousedown', startDrag);
        window.addEventListener('mousemove', onDrag);
        window.addEventListener('mouseup', stopDrag);

        // Pasang event listener untuk touch (mobile)
        floatingChar.addEventListener('touchstart', startDrag);
        window.addEventListener('touchmove', onDrag);
        window.addEventListener('touchend', stopDrag);

        // Cegah agar klik karakter tidak bentrok dengan drag
        let hasMoved = false;
        let originalStartDrag = startDrag;

        // Modifikasi startDrag untuk deteksi apakah benar-benar drag
        let dragStartTime;
        let dragStartX, dragStartY;

        floatingChar.addEventListener('mousedown', function(e) {
            dragStartTime = Date.now();
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            hasMoved = false;
        });

        window.addEventListener('mousemove', function(e) {
            if (isDragging) {
                let distance = Math.hypot(e.clientX - dragStartX, e.clientY - dragStartY);
                if (distance > 5) {
                    hasMoved = true;
                }
            }
        });

        // Simpan fungsi klik asli untuk karakter
        let originalClick = null;

        // ========== FITUR BUBBLE CHAT ==========
        let pesan = [
            "👋 Halo! Selamat datang!",
            "📋 Jangan lupa kembalikan alat tepat waktu!",
            "⚠️ Telat kena denda Rp 5.000/hari",
            "💡 Kembalikan sesuai jadwal ya!",
            "😊 Ada yang bisa saya bantu?",
            "🎉 Selamat meminjam alat!"
        ];
        let urutan = 0;
        let timer;

        let karakter = document.getElementById('char');
        let bubble = document.getElementById('bubble');
        let tutup = document.getElementById('close');
        let tempatPesan = document.getElementById('msg');

        // Fungsi tampilkan bubble
        function tampilkanPesan(teks) {
            tempatPesan.innerHTML = teks;
            bubble.classList.add('show');

            if (timer) clearTimeout(timer);
            timer = setTimeout(() => {
                bubble.classList.remove('show');
            }, 4000);
        }

        // Fungsi untuk karakter diklik (bukan drag)
        karakter.parentElement.addEventListener('click', function(e) {
            // Jika tidak terjadi perpindahan (bukan drag), tampilkan pesan
            if (!hasMoved) {
                tampilkanPesan(pesan[urutan]);
                urutan = (urutan + 1) % pesan.length;

                // Efek klik
                karakter.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    karakter.style.transform = 'scale(1)';
                }, 150);
            }
            hasMoved = false;
        });

        // Tombol tutup
        tutup.onclick = function(e) {
            e.stopPropagation();
            bubble.classList.remove('show');
            if (timer) clearTimeout(timer);
        }

        // Pesan sambutan saat buka halaman
        setTimeout(() => {
            tampilkanPesan("👋 Selamat datang di MuziTools!");
        }, 1500);

        // Set cursor default
        floatingChar.style.cursor = 'grab';
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
