{{-- File: resources/views/permintaan/index.blade.php --}}
{{-- CLEAN VERSION - Format tanggal dd/mm/yyyy + Kategori Field --}}
@extends('layouts.app')

@section('title', 'Daftar Permintaan')

@section('content')
<div class="container-fluid py-4">

    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">
                        @if(Auth::user()->isAdminGudang())
                            Permintaan Saya
                        @elseif(Auth::user()->isPurchasing())
                            Permintaan Masuk untuk Review
                        @else
                            Semua Permintaan Barang
                        @endif
                    </h3>
                    <p class="text-muted mb-0">
                        Menampilkan {{ $permintaans->total() }} permintaan
                    </p>
                </div>
                
                @if(Auth::user()->isAdminGudang())
                    <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Permintaan Baru
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label small mb-1 text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1 text-muted">Prioritas</label>
                    <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Prioritas</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Sangat Urgent</option>
                        <option value="penting" {{ request('priority') == 'penting' ? 'selected' : '' }}>Penting</option>
                        <option value="routine" {{ request('priority') == 'routine' ? 'selected' : '' }}>Rutin</option>
                        <option value="non_routine" {{ request('priority') == 'non_routine' ? 'selected' : '' }}>Non Rutin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1 text-muted">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="sparepart" {{ request('kategori') == 'sparepart' ? 'selected' : '' }}>Sparepart</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1 text-muted">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Cari nomor atau judul..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    @if(request()->hasAny(['status', 'priority', 'kategori', 'search']))
                        <a href="{{ route('permintaan.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            
            @if($permintaans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="280">Nomor / Judul Permintaan</th>
                                @unless(Auth::user()->isAdminGudang())
                                    <th width="150">Pemohon</th>
                                @endunless
                                <th width="100" class="text-center">Kategori</th>
                                <th width="110">Tgl Permintaan</th>
                                <th width="110">Tgl Dibutuhkan</th>
                                <th width="70" class="text-center">Items</th>
                                <th width="110" class="text-center">Prioritas</th>
                                <th width="90" class="text-center">Status</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaans as $index => $permintaan)
                                <tr class="animate-row">
                                    <td class="text-center">
                                        <span class="text-muted small">{{ $permintaans->firstItem() + $index }}</span>
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="badge bg-secondary small">{{ $permintaan->nomor_permintaan }}</span>
                                        </div>
                                        <div class="fw-semibold text-dark">{{ $permintaan->judul_permintaan }}</div>
                                        @if($permintaan->catatan_permintaan)
                                            <small class="text-muted d-block mt-1">{{ Str::limit($permintaan->catatan_permintaan, 50) }}</small>
                                        @endif
                                    </td>
                                    @unless(Auth::user()->isAdminGudang())
                                        <td>
                                            <div class="fw-semibold">{{ $permintaan->user->name }}</div>
                                            <small class="text-muted">
                                                {{ $permintaan->user->role === 'admin_gudang_umum' ? 'Gudang Umum' : 'Gudang Sparepart' }}
                                            </small>
                                        </td>
                                    @endunless
                                    <td class="text-center">
                                        @php
                                            $kategori = $permintaan->user->role === 'admin_gudang_sparepart' ? 'sparepart' : 'umum';
                                        @endphp
                                        @if($kategori === 'sparepart')
                                            <span class="badge bg-dark">
                                                <i class="fas fa-cog"></i> Sparepart
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-box"></i> Umum
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $permintaan->tanggal_permintaan->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $permintaan->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $permintaan->tanggal_dibutuhkan->format('d/m/Y') }}</div>
                                        @php
                                            $diff = now()->diffInDays($permintaan->tanggal_dibutuhkan, false);
                                        @endphp
                                        @if($diff < 0)
                                            <span class="badge bg-danger small">Terlewat</span>
                                        @elseif($diff <= 3)
                                            <span class="badge bg-warning small">{{ $diff }} hari</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="fs-5 fw-bold text-primary">{{ $permintaan->total_items }}</div>
                                        @if($permintaan->approved_items > 0 || $permintaan->rejected_items > 0)
                                            <small class="text-success">✓ {{ $permintaan->approved_items }}</small>
                                            @if($permintaan->rejected_items > 0)
                                                <small class="text-danger">✕ {{ $permintaan->rejected_items }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($permintaan->tingkat_prioritas)
                                            @case('urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                                @break
                                            @case('penting')
                                                <span class="badge bg-warning text-dark">Penting</span>
                                                @break
                                            @case('routine')
                                                <span class="badge bg-success">Rutin</span>
                                                @break
                                            @case('non_routine')
                                                <span class="badge bg-info">Non Rutin</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        @switch($permintaan->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('review')
                                                <span class="badge bg-info">Review</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-warning">Partial</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('permintaan.show', $permintaan) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($permintaan->status === 'draft' && $permintaan->user_id === Auth::id())
                                                <a href="{{ route('permintaan.edit', $permintaan) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $permintaan->id }}, '{{ addslashes($permintaan->judul_permintaan) }}')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($permintaans->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Menampilkan {{ $permintaans->firstItem() }} - {{ $permintaans->lastItem() }} dari {{ $permintaans->total() }} permintaan
                            </div>
                            <div>
                                {{ $permintaans->links() }}
                            </div>
                        </div>
                    </div>
                @endif
                
            @else
                {{-- Empty State --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">Tidak Ada Permintaan</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'priority', 'kategori', 'search']))
                            Tidak ditemukan permintaan dengan filter yang dipilih.
                        @else
                            @if(Auth::user()->isAdminGudang())
                                Belum ada permintaan. Buat permintaan pertama Anda!
                            @else
                                Belum ada data permintaan yang masuk.
                            @endif
                        @endif
                    </p>
                    @if(Auth::user()->isAdminGudang())
                        <a href="{{ route('permintaan.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> Buat Permintaan Baru
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

</div>

{{-- Form Delete (Hidden) --}}
<form id="formDelete" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Confirm Delete
function confirmDelete(id, judul) {
    Swal.fire({
        title: 'Hapus Permintaan?',
        html: `
            <div class="text-start">
                <p class="mb-2"><strong>Permintaan:</strong> ${judul}</p>
                <div class="alert alert-danger py-2 mb-0 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Semua item akan ikut terhapus!
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-3',
            confirmButton: 'btn-lg',
            cancelButton: 'btn-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('formDelete');
            form.action = `/permintaan/${id}`;
            form.submit();
            
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    /* Smooth Animations */
    .animate-row {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
        transform: scale(1.001);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    /* Card Styling */
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    /* Table Styling */
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding: 0.9rem 0.75rem;
        white-space: nowrap;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    /* Badge Improvements */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.75em;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
    }
    
    /* Button Improvements */
    .btn-group-sm .btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }
    
    .btn-outline-primary {
        transition: all 0.2s ease;
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
    }
    
    .btn-outline-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.25);
    }
    
    .btn-outline-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.25);
    }
    
    /* Form Control */
    .form-select:focus,
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.15);
    }
    
    .form-select-sm,
    .form-control-sm {
        border-radius: 6px;
        border-color: #e3e6f0;
    }
    
    /* Empty State */
    .fas.fa-inbox {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    /* Pagination */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border-radius: 6px;
        margin: 0 2px;
        border-color: #e3e6f0;
        color: #4e73df;
    }
    
    .page-link:hover {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.8rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.3em 0.6em;
        }
    }
    
    /* Loading animation for delete */
    .swal2-loading {
        border-color: #4e73df transparent #4e73df transparent;
    }
</style>
@endpush 