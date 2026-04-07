<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Alat</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/1600x900/?laboratory,workshop');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            border-radius: 0 0 20px 20px;
        }

        .feature-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SIPINJAM</a>
            <div class="ms-auto">
                <a href="{{ route('login') }}" class="btn btn-primary px-4">Login</a>
            </div>
        </div>
    </nav>
    <div class="hero-section text-center mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold">Peminjaman Alat Jadi Lebih Mudah</h1>
            <p class="lead mb-4">Sistem manajemen peminjaman alat laboratorium dan bengkel sekolah yang terintegrasi,
                cepat, dan transparan.</p>
            <a href="{{ route('login') }}" class="btn btn-lg btn-warning fw-bold px-5">Mulai Peminjaman</a>
        </div>
    </div>
    <div class="container mb-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 py-4">
                    <div class="card-body">
                        <div class="feature-icon"> </div>
                        <h4 class="card-title">Cari Alat</h4>
                        <p class="card-text text-muted">Cek ketersediaan stok alat secara real-time tanpa perlu
                            bolak-balik ke ruang penyimpanan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 py-4">
                    <div class="card-body">
                        <div class="feature-icon"> </div>
                        <h4 class="card-title">Ajukan Pinjaman</h4>
                        <p class="card-text text-muted">Proses pengajuan peminjaman yang praktis melalui sistem dan
                            persetujuan petugas yang cepat.</p>

                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 py-4">
                    <div class="card-body">
                        <div class="feature-icon"> </div>
                        <h4 class="card-title">Pengembalian</h4>
                        <p class="card-text text-muted">Sistem monitoring pengembalian alat yang terstruktur untuk
                            menghindari kehilangan aset.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <small>&copy; {{ date('Y') }} Sistem Peminjaman Alat. Dibuat dengan Laravel.</small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
