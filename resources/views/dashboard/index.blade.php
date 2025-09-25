@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="fw-bold text-primary mb-1">Selamat Datang, {{ $user->name }}!</h3>
                        <p class="text-muted mb-0">
                            Role: <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                        </p>
                        <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-user-circle fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user->isAdminGudang())
        <!-- Admin Gudang Dashboard -->
        <div class="col-md-4 mb-4">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $total_permintaan }}</h2>
                    <p class="mb-0">Total Permintaan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $permintaan_pending }}</h2>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-check-circle fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $permintaan_approved }}</h2>
                    <p class="mb-0">Approved</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions untuk Admin Gudang -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('permintaan.create') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-plus me-2"></i>Buat Permintaan Baru
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary btn-lg w-100 mb-3">
                                <i class="fas fa-list me-2"></i>Lihat Semua Permintaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Info -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    @if($user->role === 'admin_gudang_umum')
                        <div class="alert alert-info border-0">
                            <h6 class="fw-bold"><i class="fas fa-boxes me-2"></i>Admin Gudang Umum</h6>
                            <p class="mb-0">Anda dapat membuat permintaan untuk barang-barang umum seperti: ATK, peralatan kantor, bahan habis pakai, dll.</p>
                        </div>
                    @else
                        <div class="alert alert-warning border-0">
                            <h6 class="fw-bold"><i class="fas fa-cogs me-2"></i>Admin Gudang Sparepart</h6>
                            <p class="mb-0">Anda dapat membuat permintaan untuk sparepart mesin, komponen elektronik, dan suku cadang lainnya.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @elseif($user->isPurchasing())
        <!-- Purchasing Dashboard -->
        <div class="col-md-4 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $total_permintaan_masuk }}</h2>
                    <p class="mb-0">Permintaan Masuk</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-file-invoice fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $total_po_dibuat }}</h2>
                    <p class="mb-0">Total PO Dibuat</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-edit fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $po_draft }}</h2>
                    <p class="mb-0">PO Draft</p>
                </div>
            </div>
        </div>

    @else
        <!-- Manager Dashboard -->
        <div class="col-md-4 mb-4">
            <div class="card stats-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $total_permintaan }}</h2>
                    <p class="mb-0">Total Permintaan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-file-invoice-dollar fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $total_po }}</h2>
                    <p class="mb-0">Total PO</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center p-4">
                    <i class="fas fa-hourglass-half fa-3x mb-3 opacity-75"></i>
                    <h2 class="display-4 fw-bold mb-1">{{ $permintaan_pending }}</h2>
                    <p class="mb-0">Pending Review</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Recent Activity (untuk semua role) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clock fa-2x mb-3 opacity-50"></i>
                    <p>Fitur aktivitas terbaru akan segera hadir</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection