<?php
// File: resources/views/permintaan/index.blade.php
?>
@extends('layouts.dashboard')

@section('title', 'Daftar Permintaan')
@section('page-title', 'Daftar Permintaan Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Permintaan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    @if(Auth::user()->isAdminGudang())
                        Permintaan Saya
                    @elseif(Auth::user()->isPurchasing())
                        Permintaan Masuk untuk Review
                    @else
                        Semua Permintaan Barang
                    @endif
                </h5>
                
                @can('create-permintaan')
                    <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Buat Permintaan Baru
                    </a>
                @endcan
            </div>
            
            <div class="card-body">
                @if($permintaans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @unless(Auth::user()->isAdminGudang())
                                        <th>Pemohon</th>
                                    @endunless
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaans as $index => $permintaan)
                                    <tr>
                                        <td>{{ $permintaans->firstItem() + $index }}</td>
                                        @unless(Auth::user()->isAdminGudang())
                                            <td>
                                                <div>
                                                    <strong>{{ $permintaan->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $permintaan->user->email }}</small>
                                                </div>
                                            </td>
                                        @endunless
                                        <td>
                                            <strong>{{ $permintaan->nama_barang }}</strong>
                                            @if($permintaan->keterangan)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($permintaan->keterangan, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $permintaan->kategori === 'sparepart' ? 'bg-warning' : 'bg-info' }}">
                                                {{ ucfirst($permintaan->kategori) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($permintaan->jumlah) }} {{ $permintaan->satuan }}</td>
                                        <td>{{ $permintaan->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            @switch($permintaan->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('review')
                                                    <span class="badge bg-info">Review</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('view-permintaan', $permintaan)
                                                    <a href="{{ route('permintaan.show', $permintaan) }}" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('edit-permintaan', $permintaan)
                                                    <a href="{{ route('permintaan.edit', $permintaan) }}" 
                                                       class="btn btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @if(Auth::user()->isPurchasing() && $permintaan->status === 'pending')
                                                    <button type="button" 
                                                            class="btn btn-outline-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal{{ $permintaan->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $permintaans->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada permintaan</h5>
                        @can('create-permintaan')
                            <a href="{{ route('permintaan.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-1"></i>Buat Permintaan Pertama
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Review untuk Purchasing -->
@if(Auth::user()->isPurchasing())
    @foreach($permintaans as $permintaan)
        @if($permintaan->status === 'pending')
            <div class="modal fade" id="reviewModal{{ $permintaan->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('permintaan.review', $permintaan) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Review Permintaan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Status Review</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan Review</label>
                                    <textarea name="catatan_review" class="form-control" rows="3" placeholder="Berikan catatan untuk keputusan ini..."></textarea>
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