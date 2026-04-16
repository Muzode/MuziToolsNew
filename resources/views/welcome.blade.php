@extends('layouts.app')
@section('content')
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistem Peminjaman Alat | TOOLZ</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Google Fonts - Poppins -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- CSS External yang sudah dimodifikasi (dengan variabel dinamis) -->
        <link href="{{ asset('resources/css/app.css') }}" rel="stylesheet">

        <style>
            /* CSS Khusus untuk halaman welcome (melengkapi dari app.css) */

            /* Hero Section */
            .hero-section {
                padding: 80px 0 60px 0;
                position: relative;
            }

            .hero-section h1 {
                background: linear-gradient(135deg, var(--text-on-light) 0%, var(--text-muted-on-light) 50%, #5A7B8C 100%);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                font-size: 3rem;
                margin-bottom: 20px;
                font-weight: 700;
            }

            /* Feature Icon - Kustom untuk halaman welcome */
            .feature-icon {
                width: 80px;
                height: 80px;
                background: var(--gradient-pastel);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                transition: all 0.3s ease;
            }

            .feature-icon i {
                font-size: 40px;
                color: var(--text-on-dark);
                /* Background gelap -> teks putih */
            }

            .card:hover .feature-icon {
                transform: scale(1.05);
                box-shadow: 0 8px 20px rgba(68, 97, 158, 0.3);
            }

            /* Card custom styling untuk welcome page */
            .welcome-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                border-radius: 28px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                overflow: hidden;
                margin-bottom: 25px;
            }

            .welcome-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
                background: rgba(255, 255, 255, 0.95);
            }

            .welcome-card .card-body {
                padding: 32px 24px;
                color: var(--text-on-light);
            }

            .welcome-card .card-title {
                color: var(--text-on-light);
                font-weight: 600;
                margin-top: 16px;
                margin-bottom: 12px;
            }

            .welcome-card .card-text {
                color: var(--text-muted-on-light);
                line-height: 1.6;
            }

            /* Button kustom untuk welcome page */
            .btn-welcome {
                background: var(--gradient-sand);
                color: var(--text-on-light) !important;
                box-shadow: 0 4px 12px rgba(220, 134, 108, 0.3);
            }

            .btn-welcome:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(220, 134, 108, 0.5);
                color: var(--text-on-light) !important;
            }

            /* Animasi */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }

            .welcome-card {
                animation: fadeInUp 0.5s ease-out;
                animation-fill-mode: both;
            }

            .welcome-card:nth-child(1) {
                animation-delay: 0.1s;
            }

            .welcome-card:nth-child(2) {
                animation-delay: 0.2s;
            }

            .welcome-card:nth-child(3) {
                animation-delay: 0.3s;
            }

            /* Navbar scrolled effect */
            .navbar.scrolled {
                background: var(--pastel-blue) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .hero-section {
                    padding: 50px 0 40px 0;
                }

                .hero-section h1 {
                    font-size: 2rem;
                }

                .hero-section .lead {
                    font-size: 1rem;
                }

                .welcome-card .card-body {
                    padding: 24px 20px;
                }

                .feature-icon {
                    width: 65px;
                    height: 65px;
                }

                .feature-icon i {
                    font-size: 32px;
                }
            }
        </style>
    </head>

    <body>
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fade-in-up">TOOLZ</h1>
                <p class="lead mb-4 fade-in-up" style="animation-delay: 0.1s; color: var(--text-muted-on-light);">
                    <i class="fas fa-tools me-2"></i>
                    Sistem manajemen peminjaman alat sarana sekolah yang terintegrasi,
                    cepat, dan transparan.
                </p>
                <a href="{{ route('login') }}" class="btn btn-welcome btn-lg fw-bold px-5" style="animation-delay: 0.2s;"
                    onclick="showLoginAlert(event)">
                    <i class="fas fa-hand-peace me-2"></i>Mulai Peminjaman
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="container mb-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="welcome-card h-100 text-center">
                        <div class="card-body">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4 class="card-title">Cari Alat</h4>
                            <p class="card-text">Cek ketersediaan stok alat secara real-time tanpa perlu bolak-balik ke
                                ruang penyimpanan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="welcome-card h-100 text-center">
                        <div class="card-body">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4 class="card-title">Ajukan Pinjaman</h4>
                            <p class="card-text">Proses pengajuan peminjaman yang praktis melalui sistem dan persetujuan
                                petugas yang cepat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="welcome-card h-100 text-center">
                        <div class="card-body">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <h4 class="card-title">Pengembalian</h4>
                            <p class="card-text">Sistem monitoring pengembalian alat yang terstruktur untuk menghindari
                                kehilangan aset.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
@endsection
