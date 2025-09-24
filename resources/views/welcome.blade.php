<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Sistem Inventory Terintegrasi</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .bg-circle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .bg-circle:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 10%;
            right: 30%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Header */
        .welcome-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            z-index: 100;
        }

        .nav-link-custom {
            color: white !important;
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }

        .nav-link-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            color: white !important;
        }

        .nav-link-primary {
            background: var(--primary) !important;
            color: white !important;
        }

        .nav-link-primary:hover {
            background: var(--primary-dark) !important;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        /* Main content */
        .welcome-main {
            padding-top: 100px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .welcome-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(10px);
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            background: var(--success);
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }

        .feature-icon::after {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow);
            border: none;
        }

        .btn-primary-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-secondary-custom {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 1rem 2rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-secondary-custom:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .visual-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow);
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .visual-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: rotate 4s linear infinite;
        }

        .visual-card h2,
        .visual-card p {
            position: relative;
            z-index: 2;
        }

        .visual-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .visual-card p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2rem;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2.5rem;
            }

            .welcome-subtitle {
                font-size: 1.1rem;
            }

            .visual-card {
                padding: 2rem;
                margin-top: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation classes */
        .slide-in-left {
            animation: slideInLeft 1s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 1s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle"></div>
    </div>

    <!-- Header -->
    <header class="welcome-header">
        <div class="container">
            @if (Route::has('login'))
                <nav class="d-flex justify-content-end">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link-custom nav-link-primary">
                            🏠 Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link-custom">
                            🔐 Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link-custom nav-link-primary">
                                ✨ Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="welcome-main">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 slide-in-left">
                    <h1 class="welcome-title">Sistem Inventory SK</h1>
                    <p class="welcome-subtitle">
                        Sistem manajemen inventory PT.Sumatera Kemasindo yang terintegrasi untuk permintaan barang dan purchase order hingga laporan akhir. 
                        Solusi digital terpadu untuk efisiensi operasional gudang dan purchasing.
                    </p>
                    
                    <div class="mb-4">
                        <div class="feature-item">
                            <span class="feature-icon"></span>
                            <span class="feature-text">
                                Manajemen Permintaan Barang untuk Admin Gudang
                            </span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"></span>
                            <span class="feature-text">
                                Review dan Approval untuk Tim Purchasing
                            </span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"></span>
                            <span class="feature-text">
                                Dashboard Monitoring untuk Management
                            </span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon"></span>
                            <span class="feature-text">
                                Tracking Real-time dan Reporting Otomatis
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 flex-wrap">
                        @guest
                            <a href="{{ route('login') }}" class="btn-primary-custom">
                                🚀 Masuk ke Sistem
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-primary-custom">
                                🏠 Ke Dashboard
                            </a>
                        @endguest
                        <a href="#features" class="btn-secondary-custom">
                            📖 Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6 slide-in-right">
                    <div class="visual-card">
                        <h2>📦 Inventory SK</h2>
                        <p>Sistem Manajemen Inventory Terintegrasi</p>
                        
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Monitoring</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">100%</div>
                                <div class="stat-label">Digital</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">Real-time</div>
                                <div class="stat-label">Updates</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">Multi</div>
                                <div class="stat-label">User Role</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax effect for background circles
            document.addEventListener('mousemove', function(e) {
                const circles = document.querySelectorAll('.bg-circle');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                circles.forEach((circle, index) => {
                    const speed = (index + 1) * 0.5;
                    const xPos = (x * speed * 50) - 25;
                    const yPos = (y * speed * 50) - 25;
                    circle.style.transform = `translate(${xPos}px, ${yPos}px)`;
                });
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add loading animation to buttons
            document.querySelectorAll('.btn-primary-custom').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (this.href && !this.href.includes('#')) {
                        this.innerHTML = '⏳ Loading...';
                    }
                });
            });
        });
    </script>
</body>
</html>