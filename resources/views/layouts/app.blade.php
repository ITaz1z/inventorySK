{{-- File: resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Inventory SK') }}@hasSection('title') - @yield('title')@endif</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Toastify untuk notifikasi ringan -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
        }
        
        /* Card Hover Effect */
        .card-hover {
            transition: all 0.2s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Navigation Active State */
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border-radius: 6px;
            font-weight: 600;
        }
        
        /* Badge Notifikasi */
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Alert Custom */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success { border-left-color: #28a745; }
        .alert-danger { border-left-color: #dc3545; }
        .alert-warning { border-left-color: #ffc107; }
        .alert-info { border-left-color: #17a2b8; }
        
        /* Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        /* Quick Stats Badge */
        .quick-stats {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 4px 8px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                    <i class="fas fa-boxes me-2"></i>
                    {{ config('app.name', 'Inventory SK') }}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Left Navigation -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                </a>
                            </li>
                            
                            @if(Auth::user()->isAdminGudang())
                                <!-- Admin Gudang Menu -->
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('permintaan.index') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                                        <i class="fas fa-list me-1"></i> My Requests
                                        @php $myDrafts = Auth::user()->permintaanHeaders()->where('status', 'draft')->count(); @endphp
                                        @if($myDrafts > 0)
                                            <span class="badge bg-warning text-dark ms-1 notification-badge">{{ $myDrafts }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('permintaan.create') ? 'active' : '' }}" href="{{ route('permintaan.create') }}">
                                        <i class="fas fa-plus-circle me-1"></i> New Request
                                    </a>
                                </li>
                                
                            @elseif(Auth::user()->isPurchasing())
                                <!-- Purchasing Menu -->
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('permintaan.*') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                                        <i class="fas fa-clipboard-check me-1"></i> Review Queue
                                        @php $pending = \App\Models\PermintaanHeader::where('status', 'pending')->count(); @endphp
                                        @if($pending > 0)
                                            <span class="badge bg-danger ms-1 notification-badge">{{ $pending }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Purchase Orders
                                        @php $myDraftPO = Auth::user()->purchaseOrders()->where('status', 'draft')->count(); @endphp
                                        @if($myDraftPO > 0)
                                            <span class="badge bg-info ms-1">{{ $myDraftPO }}</span>
                                        @endif
                                    </a>
                                </li>
                                
                            @else
                                <!-- Manager Menu -->
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('permintaan.*') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                                        <i class="fas fa-eye me-1"></i> All Requests
                                        @php $urgentItems = \App\Models\PermintaanHeader::where('tingkat_prioritas', 'urgent')->whereIn('status', ['pending', 'review'])->count(); @endphp
                                        @if($urgentItems > 0)
                                            <span class="badge bg-danger ms-1 notification-badge">{{ $urgentItems }} Urgent</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">
                                        <i class="fas fa-chart-line me-1"></i> PO Reports
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Navigation -->
                    <ul class="navbar-nav">
                        @auth
                            <!-- Quick Notifications Bell -->
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" title="Notifications">
                                    <i class="fas fa-bell"></i>
                                    @php
                                        $notifCount = 0;
                                        if(Auth::user()->isAdminGudang()) {
                                            $notifCount = Auth::user()->permintaanHeaders()->where('status', 'review')->count();
                                        } elseif(Auth::user()->isPurchasing()) {
                                            $notifCount = \App\Models\PermintaanHeader::where('status', 'pending')->count();
                                        }
                                    @endphp
                                    @if($notifCount > 0)
                                        <span class="badge bg-danger notification-badge" style="font-size: 0.6em; position: absolute; top: 2px; right: 2px;">{{ $notifCount }}</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                    <li><h6 class="dropdown-header"><i class="fas fa-bell me-2"></i>Notifications</h6></li>
                                    
                                    @if(Auth::user()->isAdminGudang())
                                        @php $reviewItems = Auth::user()->permintaanHeaders()->where('status', 'review')->latest()->limit(3)->get(); @endphp
                                        @forelse($reviewItems as $item)
                                            <li>
                                                <a class="dropdown-item small" href="{{ route('permintaan.show', $item) }}">
                                                    <i class="fas fa-eye text-info me-2"></i>
                                                    <div>
                                                        <strong>{{ $item->judul_permintaan }}</strong>
                                                        <br><small class="text-muted">Being reviewed</small>
                                                    </div>
                                                </a>
                                            </li>
                                        @empty
                                            <li><span class="dropdown-item-text text-muted small">No new notifications</span></li>
                                        @endforelse
                                        
                                    @elseif(Auth::user()->isPurchasing())
                                        @php $pendingItems = \App\Models\PermintaanHeader::where('status', 'pending')->latest()->limit(3)->get(); @endphp
                                        @forelse($pendingItems as $item)
                                            <li>
                                                <a class="dropdown-item small" href="{{ route('permintaan.show', $item) }}">
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    <div>
                                                        <strong>{{ $item->judul_permintaan }}</strong>
                                                        <br><small class="text-muted">Needs review</small>
                                                    </div>
                                                </a>
                                            </li>
                                        @empty
                                            <li><span class="dropdown-item-text text-muted small">No pending reviews</span></li>
                                        @endforelse
                                        
                                    @else
                                        <li><span class="dropdown-item-text text-muted small">Manager notifications coming soon</span></li>
                                    @endif
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center small" href="{{ route('dashboard') }}">View All</a></li>
                                </ul>
                            </li>
                            
                            <!-- User Menu -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <div class="d-none d-md-block">
                                        <div>{{ Auth::user()->name }}</div>
                                        <small class="quick-stats">{{ Auth::user()->getRoleLabel() }}</small>
                                    </div>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- User Info -->
                                    <div class="dropdown-item-text border-bottom pb-2 mb-2">
                                        <strong>{{ Auth::user()->name }}</strong><br>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                    
                                    <!-- Quick Stats -->
                                    @if(Auth::user()->isAdminGudang())
                                        @php
                                            $stats = [
                                                'total' => Auth::user()->permintaanHeaders()->count(),
                                                'pending' => Auth::user()->permintaanHeaders()->where('status', 'pending')->count()
                                            ];
                                        @endphp
                                        <div class="dropdown-item-text">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <strong class="text-primary">{{ $stats['total'] }}</strong>
                                                    <br><small>Total</small>
                                                </div>
                                                <div class="col-6">
                                                    <strong class="text-info">{{ $stats['pending'] }}</strong>
                                                    <br><small>Pending</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    
                                    <!-- Logout -->
                                    <a class="dropdown-item text-danger" href="#" onclick="logout()">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Simple Footer -->
        <footer class="bg-light py-3 mt-5 border-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted">Inventory Management System</small>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toastify untuk notifikasi -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script>
        // Auto-hide flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 4000);
            });
        });
        
        // Simple logout function
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                document.getElementById('logout-form').submit();
            }
        }
        
        // Show success toast
        function showToast(message, type = 'success') {
            const bgColor = {
                'success': 'linear-gradient(to right, #00b09b, #96c93d)',
                'error': 'linear-gradient(to right, #ff5f6d, #ffc371)',
                'warning': 'linear-gradient(to right, #f7971e, #ffd200)',
                'info': 'linear-gradient(to right, #667eea, #764ba2)'
            };
            
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: bgColor[type] || bgColor.success,
                    borderRadius: '8px'
                }
            }).showToast();
        }
        
        // Show toasts for session messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if($errors->any())
            showToast('{{ $errors->first() }}', 'error');
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>