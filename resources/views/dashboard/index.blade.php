@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Dashboard</h1>
                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            </div>
        </div>
    </div>
    
    {{-- Stats Cards --}}
    <div class="row mb-4">
        @if($user->isAdminGudang())
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Total Permintaan</h6>
                                <h3 class="mb-0">{{ $total_permintaan ?? 0 }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-warning mb-1">Pending</h6>
                                <h3 class="mb-0">{{ $permintaan_pending ?? 0 }}</h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-success mb-1">Approved</h6>
                                <h3 class="mb-0">{{ $permintaan_approved ?? 0 }}</h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isPurchasing())
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Permintaan Masuk</h6>
                                <h3 class="mb-0">{{ $total_permintaan_masuk ?? 0 }}</h3>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-inbox fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Total PO</h6>
                                <h3 class="mb-0">{{ $total_po_dibuat ?? 0 }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-file-invoice fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-secondary mb-1">Draft PO</h6>
                                <h3 class="mb-0">{{ $po_draft ?? 0 }}</h3>
                            </div>
                            <div class="text-secondary">
                                <i class="fas fa-edit fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Total Permintaan</h6>
                                <h3 class="mb-0">{{ $total_permintaan ?? 0 }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Total PO</h6>
                                <h3 class="mb-0">{{ $total_po ?? 0 }}</h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-warning mb-1">Pending Review</h6>
                                <h3 class="mb-0">{{ $permintaan_pending ?? 0 }}</h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Menu Utama</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($user->isAdminGudang())
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('permintaan.index') }}" class="text-decoration-none">
                                    <div class="card border border-primary h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Kelola Permintaan Barang</h5>
                                            <p class="card-text text-muted">Buat dan kelola permintaan barang baru</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @elseif($user->isPurchasing())
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('permintaan.index') }}" class="text-decoration-none">
                                    <div class="card border border-info h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-tasks fa-3x text-info mb-3"></i>
                                            <h5 class="card-title">Review Permintaan</h5>
                                            <p class="card-text text-muted">Review permintaan barang masuk</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('purchase-orders.index') }}" class="text-decoration-none">
                                    <div class="card border border-primary h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-invoice-dollar fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Kelola Purchase Order</h5>
                                            <p class="card-text text-muted">Buat dan kelola PO</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('permintaan.index') }}" class="text-decoration-none">
                                    <div class="card border border-success h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                            <h5 class="card-title">Monitor Permintaan</h5>
                                            <p class="card-text text-muted">Pantau semua permintaan barang</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('purchase-orders.index') }}" class="text-decoration-none">
                                    <div class="card border border-primary h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Monitor Purchase Order</h5>
                                            <p class="card-text text-muted">Pantau semua purchase order</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection