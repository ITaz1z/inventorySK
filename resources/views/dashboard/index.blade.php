{{-- File: resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Welcome Header --}}
    <div class="row mb-4">
        <div class="col-12">    
            <h3 class="mb-1">👋 Selamat Datang, {{ $user->name }}!</h3>
            <p>{{ ucwords(str_replace('_', ' ', $user->role)) }}</p>
        </div>
    </div>

    @if($user->isAdminGudang())
        {{-- ADMIN GUDANG DASHBOARD --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Permintaan</p>
                                <h3 class="mb-0">{{ $total_permintaan }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Draft</p>
                                <h3 class="mb-0 text-secondary">{{ $permintaan_draft }}</h3>
                            </div>
                            <div class="text-secondary">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Pending Review</p>
                                <h3 class="mb-0 text-warning">{{ $permintaan_pending }}</h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Approved</p>
                                <h3 class="mb-0 text-success">{{ $permintaan_approved }}</h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Permintaan --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Permintaan Terbaru</h6>
                        <a href="{{ route('permintaan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        @if($recent_permintaan->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recent_permintaan as $item)
                                    <a href="{{ route('permintaan.show', $item) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-secondary me-2">{{ $item->nomor_permintaan }}</span>
                                                    @if($item->tingkat_prioritas == 'urgent')
                                                        <span class="badge bg-danger">URGENT</span>
                                                    @endif
                                                </div>
                                                <h6 class="mb-1">{{ $item->judul_permintaan }}</h6>
                                                <small class="text-muted">
                                                    {{ $item->total_items }} items • 
                                                    {{ $item->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                @switch($item->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">{{ ucfirst($item->status) }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada permintaan</p>
                                <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Buat Permintaan Baru
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Quick Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Approval Rate</small>
                                <small><strong>{{ $total_permintaan > 0 ? round(($permintaan_approved / $total_permintaan) * 100) : 0 }}%</strong></small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $total_permintaan > 0 ? ($permintaan_approved / $total_permintaan) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Permintaan Baru
                            </a>
                            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> Lihat Semua Permintaan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($user->isPurchasing())
        {{-- PURCHASING DASHBOARD --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 small opacity-75">Need Review</p>
                                <h3 class="mb-0">{{ $permintaan_pending }}</h3>
                            </div>
                            <div>
                                <i class="fas fa-exclamation-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">In Review</p>
                                <h3 class="mb-0 text-info">{{ $permintaan_review }}</h3>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">My PO Created</p>
                                <h3 class="mb-0 text-primary">{{ $total_po_dibuat }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-file-invoice fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">PO Draft</p>
                                <h3 class="mb-0 text-secondary">{{ $po_draft }}</h3>
                            </div>
                            <div class="text-secondary">
                                <i class="fas fa-pencil-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Permintaan yang Perlu Direview</h6>
                    </div>
                    <div class="card-body p-0">
                        @if($need_review->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Judul</th>
                                            <th>Pemohon</th>
                                            <th>Items</th>
                                            <th>Prioritas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($need_review as $item)
                                            <tr>
                                                <td><span class="badge bg-secondary">{{ $item->nomor_permintaan }}</span></td>
                                                <td><strong>{{ $item->judul_permintaan }}</strong></td>
                                                <td>{{ $item->user->name }}</td>
                                                <td>{{ $item->total_items }} items</td>
                                                <td>
                                                    @if($item->tingkat_prioritas == 'urgent')
                                                        <span class="badge bg-danger">URGENT</span>
                                                    @else
                                                        <span class="badge bg-info">{{ ucfirst($item->tingkat_prioritas) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('permintaan.show', $item) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Review
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Tidak ada permintaan yang perlu direview saat ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- MANAGER DASHBOARD --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1 small">Total Permintaan</p>
                        <h3 class="mb-0 text-primary">{{ $total_permintaan }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm bg-danger text-white">
                    <div class="card-body">
                        <p class="mb-1 small opacity-75">Urgent Items</p>
                        <h3 class="mb-0">{{ $permintaan_urgent }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1 small">Total PO</p>
                        <h3 class="mb-0 text-success">{{ $total_po }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-hover border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1 small">PO Sent</p>
                        <h3 class="mb-0 text-info">{{ $po_sent }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Urgent Items yang Perlu Perhatian</h6>
                    </div>
                    <div class="card-body">
                        @if($urgent_items->count() > 0)
                            <div class="list-group">
                                @foreach($urgent_items as $item)
                                    <a href="{{ route('permintaan.show', $item) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="badge bg-danger me-2">URGENT</span>
                                                <strong>{{ $item->judul_permintaan }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $item->user->name }} • {{ $item->created_at->diffForHumans() }}</small>
                                            </div>
                                            <span class="badge bg-{{ $item->status == 'pending' ? 'warning' : 'info' }}">{{ ucfirst($item->status) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Tidak ada urgent items saat ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection