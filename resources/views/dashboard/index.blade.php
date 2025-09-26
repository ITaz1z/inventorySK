@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h3 mb-3">Dashboard</h1>
            
            {{-- Welcome Message --}}
            <div class="alert alert-info mb-4">
                <h5 class="mb-1">Selamat Datang, {{ $user->name }}!</h5>
                <p class="mb-0">Role: {{ $user->getRoleLabel() }}</p>
            </div>
        </div>
    </div>

    {{-- ADMIN GUDANG DASHBOARD --}}
    @if($user->isAdminGudang())
    <div class="row">
        {{-- Stats Cards --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Permintaan</h6>
                            <h2 class="mb-0">{{ $total_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Draft</h6>
                            <h2 class="mb-0">{{ $draft_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending Review</h6>
                            <h2 class="mb-0">{{ $pending_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Approved</h6>
                            <h2 class="mb-0">{{ $approved_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        {{-- Quick Actions --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Permintaan Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recent_permintaan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Judul</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_permintaan as $permintaan)
                                    <tr>
                                        <td><small>{{ $permintaan->nomor_permintaan }}</small></td>
                                        <td>{{ Str::limit($permintaan->judul_permintaan, 30) }}</td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $permintaan->items->count() }} items</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'pending' => 'warning',
                                                    'review' => 'info',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'partial' => 'primary'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$permintaan->status] ?? 'secondary' }}">
                                                {{ $permintaan->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td><small>{{ $permintaan->created_at->diffForHumans() }}</small></td>
                                        <td>
                                            <a href="{{ route('permintaan.show', $permintaan) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada permintaan. <a href="{{ route('permintaan.create') }}">Buat permintaan pertama</a></p>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Quick Actions --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Buat Permintaan Baru
                        </a>
                        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Lihat Semua Permintaan
                        </a>
                    </div>
                    
                    <hr>
                    <h6>Statistik</h6>
                    <ul class="list-unstyled">
                        <li><small>Total Items: <strong>{{ $total_items }}</strong></small></li>
                        <li><small>Kategori: <strong>{{ $user->getGudangKategori() }}</strong></small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PURCHASING DASHBOARD --}}
    @if($user->isPurchasing())
    <div class="row">
        {{-- Stats Cards --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending Review</h6>
                            <h2 class="mb-0">{{ $pending_review }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">In Review</h6>
                            <h2 class="mb-0">{{ $in_review }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">PO Dibuat</h6>
                            <h2 class="mb-0">{{ $total_po_dibuat }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">PO Draft</h6>
                            <h2 class="mb-0">{{ $po_draft }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Permintaan Perlu Review</h5>
                </div>
                <div class="card-body">
                    @if($need_review->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Admin Gudang</th>
                                        <th>Judul</th>
                                        <th>Items</th>
                                        <th>Prioritas</th>
                                        <th>Tanggal Butuh</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($need_review as $permintaan)
                                    <tr>
                                        <td><small>{{ $permintaan->nomor_permintaan }}</small></td>
                                        <td>{{ $permintaan->user->name }}</td>
                                        <td>{{ Str::limit($permintaan->judul_permintaan, 30) }}</td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $permintaan->items->count() }} items</span>
                                        </td>
                                        <td>
                                            @php
                                                $priorityColors = [
                                                    'urgent' => 'danger',
                                                    'penting' => 'warning',
                                                    'routine' => 'info',
                                                    'non_routine' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $priorityColors[$permintaan->tingkat_prioritas] ?? 'secondary' }}">
                                                {{ $permintaan->getPriorityLabel() }}
                                            </span>
                                        </td>
                                        <td><small>{{ $permintaan->tanggal_dibutuhkan->format('d M Y') }}</small></td>
                                        <td>
                                            <a href="{{ route('permintaan.show', $permintaan) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Tidak ada permintaan yang perlu direview saat ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MANAGER DASHBOARD --}}
    @if($user->isManager())
    <div class="row">
        {{-- Stats Cards --}}
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Permintaan</h6>
                            <h2 class="mb-0">{{ $total_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total PO</h6>
                            <h2 class="mb-0">{{ $total_po }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending Review</h6>
                            <h2 class="mb-0">{{ $pending_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Urgent</h6>
                            <h2 class="mb-0">{{ $urgent_permintaan }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Summary by Kategori</h5>
                </div>
                <div class="card-body">
                    @if($summary_kategori->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Total Items</th>
                                        <th>Approved</th>
                                        <th>Pending</th>
                                        <th>Rejected</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary_kategori as $summary)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $summary->kategori === 'sparepart' ? 'warning' : 'info' }}">
                                                {{ ucfirst($summary->kategori) }}
                                            </span>
                                        </td>
                                        <td>{{ $summary->total }}</td>
                                        <td><span class="text-success">{{ $summary->approved }}</span></td>
                                        <td><span class="text-warning">{{ $summary->pending }}</span></td>
                                        <td><span class="text-danger">{{ $summary->rejected }}</span></td>
                                        <td>
                                            @php
                                                $progress = $summary->total > 0 ? round(($summary->approved / $summary->total) * 100) : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $progress }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada data permintaan.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Reports</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Lihat Semua Permintaan
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-file-invoice"></i> Lihat Purchase Orders
                        </a>
                    </div>
                    
                    <hr>
                    <h6>This Month</h6>
                    <ul class="list-unstyled">
                        <li><small>Approved: <strong>{{ $approved_this_month }}</strong></small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Auto refresh stats setiap 30 detik
setInterval(function() {
    // Optional: Implement AJAX stats refresh
    console.log('Stats refreshed');
}, 30000);
</script>
@endpush
@endsection