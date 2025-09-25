@extends('layouts.dashboard')

@section('title', 'Daftar Permintaan Barang')
@section('page-title', 'Daftar Permintaan Barang')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1">
                    @if(Auth::user()->isAdminGudang())
                        Permintaan Barang Saya
                    @elseif(Auth::user()->isPurchasing())
                        Review Permintaan Barang
                    @else
                        Semua Permintaan Barang
                    @endif
                </h4>
                <p class="text-muted mb-0">Kelola permintaan barang dengan mudah</p>
            </div>
            
            @if(Auth::user()->isAdminGudang())
                <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat Permintaan
                </a>
            @endif
        </div>

        <!-- Filter Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clipboard-list fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $permintaans->total() }}</h5>
                                <small>Total Permintaan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-warning text-white">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $permintaans->where('status', 'pending')->count() }}</h5>
                                <small>Pending</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-info text-white">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $permintaans->where('status', 'review')->count() }}</h5>
                                <small>Review</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card bg-success text-white">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $permintaans->where('status', 'approved')->count() }}</h5>
                                <small>Approved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card border-0">
            <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Data Permintaan</h6>
                    </div>
                    <div class="col-auto">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Cari permintaan..." id="searchInput">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($permintaans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4">#</th>
                                    <th class="border-0">Nama Barang</th>
                                    <th class="border-0">Kategori</th>
                                    <th class="border-0">Jumlah</th>
                                    @if(!Auth::user()->isAdminGudang())
                                        <th class="border-0">Pemohon</th>
                                    @endif
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaans as $permintaan)
                                    <tr>
                                        <td class="ps-4">{{ $loop->iteration + ($permintaans->currentPage() - 1) * $permintaans->perPage() }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $permintaan->nama_barang }}</div>
                                            @if($permintaan->keterangan)
                                                <small class="text-muted">{{ Str::limit($permintaan->keterangan, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $permintaan->kategori == 'umum' ? 'bg-primary' : 'bg-warning' }}">
                                                {{ ucfirst($permintaan->kategori) }}
                                            </span>
                                        </td>
                                        <td>{{ $permintaan->jumlah }} {{ $permintaan->satuan }}</td>
                                        @if(!Auth::user()->isAdminGudang())
                                            <td>{{ $permintaan->user->name }}</td>
                                        @endif
                                        <td>
                                            @switch($permintaan->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                    @break
                                                @case('review')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-eye me-1"></i>Review
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Rejected
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $permintaan->created_at->format('d M Y') }}</td>
                                        <td class="pe-4">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('permintaan.show', $permintaan) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(Auth::user()->isAdminGudang() && $permintaan->user_id === Auth::id() && $permintaan->status === 'pending')
                                                    <a href="{{ route('permintaan.edit', $permintaan) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('permintaan.destroy', $permintaan) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(Auth::user()->isPurchasing() && $permintaan->status === 'pending')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#reviewModal{{ $permintaan->id }}"
                                                            title="Review">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                
                                                @if(Auth::user()->isPurchasing() && $permintaan->status === 'approved' && !$permintaan->purchaseOrder)
                                                    <a href="{{ route('purchase-orders.create', ['permintaan_id' => $permintaan->id]) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       data-bs-toggle="tooltip" title="Buat PO">
                                                        <i class="fas fa-file-invoice me-1"></i>PO
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer bg-transparent">
                        {{ $permintaans->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada permintaan barang</h5>
                        <p class="text-muted">
                            @if(Auth::user()->isAdminGudang())
                                Klik tombol "Buat Permintaan" untuk membuat permintaan barang baru
                            @else
                                Menunggu permintaan dari admin gudang
                            @endif
                        </p>
                        @if(Auth::user()->isAdminGudang())
                            <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Permintaan Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Modal untuk Purchasing -->
@if(Auth::user()->isPurchasing())
    @foreach($permintaans as $permintaan)
        @if($permintaan->status === 'pending')
            <div class="modal fade" id="reviewModal{{ $permintaan->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Review Permintaan: {{ $permintaan->nama_barang }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('permintaan.review', $permintaan) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Pemohon:</strong> {{ $permintaan->user->name }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Kategori:</strong> {{ ucfirst($permintaan->kategori) }}
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>Jumlah:</strong> {{ $permintaan->jumlah }} {{ $permintaan->satuan }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Tanggal Butuh:</strong> 
                                        {{ $permintaan->tanggal_butuh ? $permintaan->tanggal_butuh->format('d M Y') : '-' }}
                                    </div>
                                </div>
                                @if($permintaan->keterangan)
                                    <div class="mt-3">
                                        <strong>Keterangan:</strong>
                                        <p class="text-muted">{{ $permintaan->keterangan }}</p>
                                    </div>
                                @endif
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status Review</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="review">Set ke Review</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Catatan Review (Optional)</label>
                                    <textarea name="catatan_review" class="form-control" rows="3" 
                                              placeholder="Berikan catatan jika diperlukan"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        // Simple client-side search (you can implement server-side search later)
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush