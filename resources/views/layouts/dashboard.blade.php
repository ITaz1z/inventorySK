<?php
// File: resources/views/layouts/dashboard.blade.php
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Inventory SK') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="dashboard-layout">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4 class="text-white mb-0">
                <i class="fas fa-boxes me-2"></i>
                Inventory SK
            </h4>
        </div>
        
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            @if(Auth::user()->isAdminGudang())
                <a href="{{ route('permintaan.index') }}" class="sidebar-link {{ request()->routeIs('permintaan.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Permintaan Saya</span>
                </a>
                <a href="{{ route('permintaan.create') }}" class="sidebar-link">
                    <i class="fas fa-plus"></i>
                    <span>Buat Permintaan</span>
                </a>
            @endif
            
            @if(Auth::user()->isPurchasing())
                <a href="{{ route('permintaan.index') }}" class="sidebar-link {{ request()->routeIs('permintaan.*') ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i>
                    <span>Review Permintaan</span>
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Purchase Orders</span>
                </a>
            @endif
            
            @if(Auth::user()->isManager())
                <a href="{{ route('permintaan.index') }}" class="sidebar-link {{ request()->routeIs('permintaan.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Semua Permintaan</span>
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Semua PO</span>
                </a>
            @endif
        </div>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    @if(View::hasSection('breadcrumb'))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    @endif
                </div>
                
                <div class="header-actions">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg me-1"></i>
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </main>
</body>
</html>