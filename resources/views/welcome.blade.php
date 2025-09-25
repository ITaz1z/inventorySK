<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Sistem Inventory</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #1e40af;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e40af 100%);
            min-height: 100vh;
            color: white;
        }

        /* Header */
        .navbar {
            background: rgba(15, 23, 42, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.3);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(59, 130, 246, 0.2);
            color: white !important;
        }

        .btn-primary-nav {
            background: var(--primary) !important;
            border: none !important;
            color: white !important;
        }

        .btn-primary-nav:hover {
            background: var(--primary-dark) !important;
            transform: translateY(-1px);
        }

        /* Main Content */
        .hero-section {
            padding: 120px 0 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 2.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.95);
        }

        .feature-icon {
            width: 20px;
            height: 20px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-hero {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-hero-primary {
            background: var(--primary);
            color: white;
        }

        .btn-hero-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-hero-secondary {
            background: rgba(30, 41, 59, 0.8);
            color: white;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .btn-hero-secondary:hover {
            background: rgba(30, 64, 175, 0.3);
            border-color: var(--primary);
            color: white;
            text-decoration: none;
        }

        /* Info Card */
        .info-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 2.5rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
            text-align: center;
            height: 100%;
        }

        .info-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .info-card p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1.5rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .stat-item {
            background: rgba(30, 41, 59, 0.5);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .info-card {
                padding: 2rem;
                margin-top: 2rem;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: 100px 0 60px;
            }
            
            .btn-hero {
                width: 100%;
                justify-content: center;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">🔷 Inventory SK</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary-nav" href="{{ url('/dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary-nav" href="{{ route('login') }}">
                                Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Sistem Inventory SK</h1>
                    <p class="hero-subtitle">
                        Sistem manajemen inventory terintegrasi untuk PT. Sumatera Kemasindo. 
                        Kelola permintaan barang, purchase order, dan monitoring dalam satu platform.
                        <strong>Hanya untuk karyawan terdaftar.</strong>
                    </p>
                    
                    <ul class="feature-list">
                        <li class="feature-item">
                            <span class="feature-icon">👤</span>
                            <span>Khusus Karyawan Terdaftar</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>Manajemen Permintaan Barang</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>Review dan Approval Process</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span>Dashboard Monitoring Real-time</span>
                        </li>
                    </ul>
                    
                    <div class="d-flex flex-column flex-md-row gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary">
                                🏠 Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-hero btn-hero-primary">
                                🔐 Login Sistem
                            </a>
                        @endauth
                        <a href="#info" class="btn-hero btn-hero-secondary">
                            📖 Info Sistem
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="info-card" id="info">
                        <h3>🔐 Sistem Internal</h3>
                        <p>Platform manajemen inventory khusus untuk karyawan PT. Sumatera Kemasindo. Akses terbatas hanya untuk pengguna yang telah terdaftar dalam sistem.</p>
                        
                        <div class="stats">
                            <div class="stat-item">
                                <div class="stat-number">Staff</div>
                                <div class="stat-label">Only</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">Secure</div>
                                <div class="stat-label">Access</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">Real</div>
                                <div class="stat-label">Time</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">Fast</div>
                                <div class="stat-label">Process</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Simple smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });

        // Loading state for navigation buttons
        document.querySelectorAll('.btn-hero-primary').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href && !this.href.includes('#')) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '⏳ Loading...';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 3000);
                }
            });
        });
    </script>
</body>
</html>