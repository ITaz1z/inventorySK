<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - Sistem Inventory</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600" rel="stylesheet" />
        
        <!-- Styles -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="welcome-page">
        <!-- Navigation -->
        <header class="welcome-header">
            @if (Route::has('login'))
                <nav class="welcome-nav">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link nav-link-primary">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link nav-link-primary">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Content -->
        <main class="welcome-main">
            <div class="welcome-container">
                <div class="welcome-content">
                    <div class="content-text">
                        <h1 class="welcome-title">Sistem Inventory SK</h1>
                        <p class="welcome-subtitle">
                            Sistem manajemen inventory untuk permintaan barang dan purchase order yang terintegrasi.
                        </p>
                        
                        <div class="feature-list">
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
                        </div>
                        
                        <div class="welcome-actions">
                            @guest
                                <a href="{{ route('login') }}" class="btn-primary">
                                    Masuk ke Sistem
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="btn-primary">
                                    Ke Dashboard
                                </a>
                            @endguest
                        </div>
                    </div>
                    
                    <div class="content-visual">
                        <div class="visual-placeholder">
                            <h2>Inventory SK</h2>
                            <p>Sistem Manajemen Inventory</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>