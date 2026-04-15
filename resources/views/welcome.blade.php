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
    
    <style>
        /* Custom Color Variables - Tema Pastel Laut */
        :root {
            /* Warna Biru Pastel */
            --pastel-blue: #A8D8EA;
            --soft-blue: #C5E0F4;
            --powder-blue: #B8D4E3;
            --mist-blue: #D4EAF7;
            --sky-pastel: #E0F0FA;
            
            /* Warna Laut Pastel */
            --seafoam-pastel: #B5E3D5;
            --mint-pastel: #C5E8D7;
            --aqua-pastel: #C2E0F0;
            
            /* Aksen Pastel */
            --coral-pastel: #F2B5A8;
            --peach-pastel: #F5D0B8;
            --sand-pastel: #F5E6D3;
            --yellow-pastel: #F5E6B8;
            --sage-pastel: #C5D8C5;
            
            /* Gradients Pastel */
            --gradient-pastel: linear-gradient(135deg, var(--pastel-blue) 0%, var(--seafoam-pastel) 50%, var(--mint-pastel) 100%);
            --gradient-soft: linear-gradient(135deg, var(--soft-blue) 0%, var(--powder-blue) 100%);
            --gradient-warm: linear-gradient(135deg, var(--peach-pastel) 0%, var(--coral-pastel) 100%);
            --gradient-sand: linear-gradient(135deg, var(--sand-pastel) 0%, var(--yellow-pastel) 100%);
        }

        /* Global Styles */
        body {
            background: linear-gradient(135deg, #E8F4F8 0%, #D4EAF7 50%, #C5E0F4 100%);
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            position: relative;
            color: #4A6B7A;
        }

        /* Efek Awan Lembut */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 40%, rgba(255,255,255,0.3) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Container */
        .container, .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0 60px 0;
            position: relative;
        }

        .hero-section h1 {
            background: linear-gradient(135deg, #4A6B7A 0%, #6B8B9C 50%, #5A7B8C 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 3rem;
            margin-bottom: 20px;
        }

        /* Card Styles - Soft & Elegant */
        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 28px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.92);
        }

        .card-body {
            padding: 32px 24px;
        }

        .card-title {
            color: #5A7B8C;
            font-weight: 600;
            margin-top: 16px;
            margin-bottom: 12px;
        }

        .card-text {
            color: #7A9BAB;
            line-height: 1.6;
        }

        /* Feature Icon */
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
            color: #5A7B8C;
        }

        .card:hover .feature-icon {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(168, 216, 234, 0.3);
        }

        /* Button Styles - Soft & Elegant */
        .btn {
            border-radius: 50px;
            padding: 10px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            z-index: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--aqua-pastel) 100%);
            color: #4A6B7A;
            box-shadow: 0 2px 6px rgba(168, 216, 234, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--aqua-pastel) 0%, var(--pastel-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(168, 216, 234, 0.4);
            color: #3A5B6A;
        }

        .btn-warning {
            background: var(--gradient-sand);
            color: #7B6A5B;
            box-shadow: 0 4px 12px rgba(245, 230, 184, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 230, 184, 0.5);
            color: #6B5A4B;
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid var(--pastel-blue);
            color: #5A7B8C;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-pastel);
            border-color: transparent;
            transform: translateY(-2px);
        }

        /* Navbar Styles - Soft */
        .navbar {
            background: rgba(168, 216, 234, 0.95) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(168, 216, 234, 0.98) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand {
            font-weight: 700;
            color: #4A6B7A !important;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            margin-right: 8px;
            color: #5A7B8C;
        }

        /* Footer */
        footer {
            background: rgba(168, 216, 234, 0.9);
            backdrop-filter: blur(10px);
            color: #5A7B8C;
            padding: 30px 0;
            margin-top: 60px;
        }


        /* Animations */
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

        .card {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }


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
            
            .card-body {
                padding: 24px 20px;
            }
            
            .feature-icon {
                width: 65px;
                height: 65px;
            }
            
            .feature-icon i {
                font-size: 32px;
            }
            
            .btn {
                padding: 8px 20px;
                font-size: 0.85rem;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold fade-in-up">TOOLZ</h1>
            <p class="lead mb-4 text-muted fade-in-up" style="animation-delay: 0.1s;">
                Sistem manajemen peminjaman alat sarana sekolah yang terintegrasi,
                cepat, dan transparan.
            </p>
            <a href="{{ route('login') }}" class="btn btn-lg btn-warning fw-bold px-5" onclick="showLoginAlert(event)" style="animation-delay: 0.2s;">
                <i class="fas fa-hand-peace me-2"></i>Mulai Peminjaman
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 text-center">
                    <div class="card-body">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="card-title">Cari Alat</h4>
                        <p class="card-text">Cek ketersediaan stok alat secara real-time tanpa perlu bolak-balik ke ruang penyimpanan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 text-center">
                    <div class="card-body">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 class="card-title">Ajukan Pinjaman</h4>
                        <p class="card-text">Proses pengajuan peminjaman yang praktis melalui sistem dan persetujuan petugas yang cepat.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 text-center">
                    <div class="card-body">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h4 class="card-title">Pengembalian</h4>
                        <p class="card-text">Sistem monitoring pengembalian alat yang terstruktur untuk menghindari kehilangan aset.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
@endsection
