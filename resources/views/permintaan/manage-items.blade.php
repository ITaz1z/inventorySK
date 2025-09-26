{{-- File: resources/views/permintaan/manage-items.blade.php --}}
{{-- Halaman untuk mengelola items dalam permintaan --}}
@extends('layouts.app')

@section('title', 'Kelola Items Permintaan')

@section('content')
<div class="container-fluid py-4">

    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Kelola Items Permintaan</h4>
                    <p class="text-muted mb-0">Tambahkan barang-barang yang dibutuhkan</p>
                </div>
                <a href="{{ route('permintaan.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column: Header Info + Form Add Item --}}
        <div class="col-lg-5">
            
            {{-- Header Info Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Permintaan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="140" class="text-muted">Judul:</td>
                            <td><strong>{{ $permintaan->judul_permintaan }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor:</td>
                            <td><span class="badge bg-secondary">{{ $permintaan->nomor_permintaan }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Permintaan:</td>
                            <td>{{ $permintaan->tanggal_permintaan->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibutuhkan:</td>
                            <td>{{ $permintaan->tanggal_dibutuhkan->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prioritas:</td>
                            <td>
                                @switch($permintaan->tingkat_prioritas)
                                    @case('urgent')
                                        <span class="badge bg-danger">Sangat Urgent</span>
                                        @break
                                    @case('penting')
                                        <span class="badge bg-warning">Penting</span>
                                        @break
                                    @case('routine')
                                        <span class="badge bg-success">Rutin</span>
                                        @break
                                    @case('non_routine')
                                        <span class="badge bg-info">Non Rutin</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($permintaan->status) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Items:</td>
                            <td><strong>{{ $permintaan->items->count() }}</strong> item</td>
                        </tr>
                    </table>

                    @if($permintaan->catatan_permintaan)
                        <hr>
                        <small class="text-muted d-block mb-1">Catatan:</small>
                        <p class="mb-0 small">{{ $permintaan->catatan_permintaan }}</p>
                    @endif
                </div>
            </div>

            {{-- Form Add Item --}}
            @if($permintaan->canBeEdited())
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Item Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('permintaan.items.store', $permintaan) }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="formAddItem">
                        @csrf

                        {{-- Nama Barang --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nama_barang" 
                                   class="form-control" 
                                   placeholder="Contoh: Kertas A4 80gsm"
                                   required>
                            <small class="text-muted">Tulis nama barang yang jelas</small>
                        </div>

                        <div class="row">
                            {{-- Jumlah --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" 
                                       name="jumlah" 
                                       class="form-control" 
                                       min="1"
                                       placeholder="0"
                                       required>
                            </div>

                            {{-- Satuan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="satuan" 
                                       class="form-control" 
                                       placeholder="pcs, rim, box"
                                       list="satuan-list"
                                       required>
                                <datalist id="satuan-list">
                                    <option value="pcs">
                                    <option value="rim">
                                    <option value="box">
                                    <option value="unit">
                                    <option value="set">
                                    <option value="meter">
                                    <option value="liter">
                                    <option value="kg">
                                    <option value="pack">
                                    <option value="lusin">
                                </datalist>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" 
                                      class="form-control" 
                                      rows="2"
                                      placeholder="Spesifikasi, merek, atau catatan tambahan..."></textarea>
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gambar Barang</label>
                            <input type="file" 
                                   name="gambar" 
                                   class="form-control" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-muted">Max 2MB. Format: JPG, PNG, GIF</small>
                        </div>

                        {{-- Urgent Checkbox --}}
                        <div class="form-check mb-3">
                            <input type="checkbox" 
                                   name="is_urgent" 
                                   class="form-check-input" 
                                   id="isUrgent"
                                   value="1">
                            <label class="form-check-label" for="isUrgent">
                                <i class="fas fa-exclamation-triangle text-danger"></i> Tandai sebagai Urgent
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-lock"></i> Permintaan sudah tidak bisa diedit
            </div>
            @endif

        </div>

        {{-- Right Column: List Items --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Daftar Items ({{ $permintaan->items->count() }})</h6>
                    @if($permintaan->canBeEdited() && $permintaan->items->count() > 0)
                        <button type="button" class="btn btn-sm btn-primary" onclick="submitPermintaan()">
                            <i class="fas fa-paper-plane"></i> Submit Permintaan
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    
                    @if($permintaan->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nama Barang</th>
                                        <th width="100">Jumlah</th>
                                        <th width="80">Gambar</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permintaan->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->nama_barang }}</strong>
                                                @if($item->is_urgent)
                                                    <span class="badge bg-danger badge-sm">URGENT</span>
                                                @endif
                                                @if($item->keterangan)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($item->keterangan, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ number_format($item->jumlah) }}<br>
                                                <small class="text-muted">{{ $item->satuan }}</small>
                                            </td>
                                            <td>
                                                @if($item->gambar_path)
                                                    <a href="{{ Storage::url($item->gambar_path) }}" target="_blank">
                                                        <img src="{{ Storage::url($item->gambar_path) }}" 
                                                             class="img-thumbnail" 
                                                             width="50"
                                                             alt="Gambar">
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($permintaan->canBeEdited())
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" 
                                                                class="btn btn-outline-primary btn-sm"
                                                                onclick="editItem({{ $item->id }})"
                                                                title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm"
                                                                onclick="deleteItem({{ $item->id }})"
                                                                title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">Locked</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada item</h6>
                            <p class="text-muted small">Tambahkan item pertama menggunakan form di sebelah kiri</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Info Box --}}
            @if($permintaan->canBeEdited() && $permintaan->items->count() > 0)
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Langkah Terakhir:</strong> Setelah semua item ditambahkan, klik tombol 
                <strong>"Submit Permintaan"</strong> untuk mengirim ke Purchasing untuk direview.
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

@push('scripts')
<script>
$(document).ready(function() {
    // Auto focus nama barang
    $('input[name="nama_barang"]').focus();
});

// Delete Item
function deleteItem(itemId) {
    Swal.fire({
        title: 'Hapus Item?',
        text: 'Item ini akan dihapus dari permintaan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('formDelete');
            form.action = `/permintaan/{{ $permintaan->id }}/items/${itemId}`;
            form.submit();
        }
    });
}

// Edit Item (akan kita buat nanti dengan modal)
function editItem(itemId) {
    Swal.fire({
        title: 'Edit Item',
        text: 'Fitur edit akan dibuat dengan modal popup',
        icon: 'info'
    });
}

// Submit Permintaan
function submitPermintaan() {
    Swal.fire({
        title: 'Submit Permintaan?',
        html: 'Permintaan akan dikirim ke <strong>Purchasing</strong> untuk direview.<br>Setelah disubmit, Anda tidak bisa menambah/edit item lagi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Submit',
        cancelButtonText: 'Belum'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form dan submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("permintaan.submit", $permintaan) }}';
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .img-thumbnail {
        object-fit: cover;
        height: 50px;
    }
    
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush
@endsection