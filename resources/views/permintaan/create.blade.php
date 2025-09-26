{{-- File: resources/views/permintaan/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Permintaan Barang Baru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i>
                Buat Permintaan Barang Baru
            </h2>
            <p class="text-muted mb-0">Buat permintaan barang untuk kebutuhan gudang</p>
        </div>
        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Alert Errors -->
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

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-file-alt me-2"></i>
                Informasi Permintaan
            </h5>
        </div>

        <form action="{{ route('permintaan.store') }}" method="POST" id="form-permintaan">
            @csrf
            
            <div class="card-body">
                <div class="row">
                    <!-- Judul Permintaan -->
                    <div class="col-md-8 mb-3">
                        <label for="judul_permintaan" class="form-label">
                            <i class="fas fa-heading text-primary me-1"></i>
                            Judul Permintaan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('judul_permintaan') is-invalid @enderror" 
                               id="judul_permintaan" 
                               name="judul_permintaan" 
                               value="{{ old('judul_permintaan') }}"
                               placeholder="Contoh: Permintaan ATK Bulan Januari 2024"
                               maxlength="255"
                               required>
                        @error('judul_permintaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Berikan judul yang jelas dan mudah dipahami
                        </div>
                    </div>

                    <!-- Tingkat Prioritas -->
                    <div class="col-md-4 mb-3">
                        <label for="tingkat_prioritas" class="form-label">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Tingkat Prioritas <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('tingkat_prioritas') is-invalid @enderror" 
                                id="tingkat_prioritas" 
                                name="tingkat_prioritas" 
                                required>
                            <option value="">Pilih Prioritas</option>
                            <option value="urgent" {{ old('tingkat_prioritas') == 'urgent' ? 'selected' : '' }}>
                                🔴 Sangat Urgent
                            </option>
                            <option value="penting" {{ old('tingkat_prioritas') == 'penting' ? 'selected' : '' }}>
                                🟡 Penting
                            </option>
                            <option value="routine" {{ old('tingkat_prioritas') == 'routine' ? 'selected' : '' }}>
                                🟢 Rutin
                            </option>
                            <option value="non_routine" {{ old('tingkat_prioritas') == 'non_routine' ? 'selected' : '' }}>
                                🔵 Non Rutin
                            </option>
                        </select>
                        @error('tingkat_prioritas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Tanggal Dibutuhkan -->
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_dibutuhkan" class="form-label">
                            <i class="fas fa-calendar-alt text-success me-1"></i>
                            Tanggal Dibutuhkan <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control @error('tanggal_dibutuhkan') is-invalid @enderror" 
                               id="tanggal_dibutuhkan" 
                               name="tanggal_dibutuhkan" 
                               value="{{ old('tanggal_dibutuhkan') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               required>
                        @error('tanggal_dibutuhkan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Minimal H+1 dari hari ini
                        </div>
                    </div>

                    <!-- Info Pemohon -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-user text-info me-1"></i>
                            Pemohon
                        </label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ auth()->user()->name }}" 
                               readonly>
                        <div class="form-text">
                            <i class="fas fa-id-badge me-1"></i>
                            {{ auth()->user()->getRoleLabel() }}
                        </div>
                    </div>

                    <!-- Kategori Barang -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-tags text-secondary me-1"></i>
                            Kategori Barang
                        </label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ ucfirst(auth()->user()->getGudangKategori()) }}" 
                               readonly>
                        <div class="form-text">
                            <i class="fas fa-warehouse me-1"></i>
                            Otomatis berdasarkan role Anda
                        </div>
                    </div>
                </div>

                <!-- Catatan Permintaan -->
                <div class="mb-3">
                    <label for="catatan_permintaan" class="form-label">
                        <i class="fas fa-sticky-note text-warning me-1"></i>
                        Catatan Permintaan
                        <small class="text-muted">(Opsional)</small>
                    </label>
                    <textarea class="form-control @error('catatan_permintaan') is-invalid @enderror" 
                              id="catatan_permintaan" 
                              name="catatan_permintaan" 
                              rows="4"
                              placeholder="Tuliskan catatan tambahan, alasan khusus, atau detail lainnya yang perlu diketahui...">{{ old('catatan_permintaan') }}</textarea>
                    @error('catatan_permintaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-lightbulb me-1"></i>
                        Jelaskan alasan permintaan, spesifikasi khusus, atau informasi penting lainnya
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info border-left-info">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="alert-heading mb-2">Langkah Selanjutnya</h6>
                            <p class="mb-2">Setelah membuat permintaan ini, Anda akan dapat:</p>
                            <ul class="mb-0">
                                <li>Menambahkan item-item barang yang dibutuhkan</li>
                                <li>Mengatur urutan prioritas item</li>
                                <li>Mengunggah gambar untuk item tertentu</li>
                                <li>Mengirim permintaan untuk review</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Footer with Actions -->
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="fas fa-asterisk text-danger me-1" style="font-size: 0.7em;"></i>
                        <small>Field yang bertanda * wajib diisi</small>
                    </div>
                    
                    <div class="btn-group">
                        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan & Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <style>
        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .card {
            border: none;
            border-radius: 10px;
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn {
            border-radius: 6px;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .form-label {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .text-danger {
            color: #e74a3b !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto focus pada field pertama
            $('#judul_permintaan').focus();
            
            // Validasi tanggal minimal H+1
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const minDate = tomorrow.toISOString().split('T')[0];
            $('#tanggal_dibutuhkan').attr('min', minDate);
            
            // Preview prioritas dengan warna
            $('#tingkat_prioritas').on('change', function() {
                const select = $(this);
                const value = select.val();
                
                // Reset classes
                select.removeClass('border-danger border-warning border-success border-info');
                
                // Add appropriate border color
                switch(value) {
                    case 'urgent':
                        select.addClass('border-danger');
                        break;
                    case 'penting':
                        select.addClass('border-warning');
                        break;
                    case 'routine':
                        select.addClass('border-success');
                        break;
                    case 'non_routine':
                        select.addClass('border-info');
                        break;
                }
            });
            
            // Form validation sebelum submit
            $('#form-permintaan').on('submit', function(e) {
                const judul = $('#judul_permintaan').val().trim();
                const tanggal = $('#tanggal_dibutuhkan').val();
                const prioritas = $('#tingkat_prioritas').val();
                
                if (!judul || !tanggal || !prioritas) {
                    e.preventDefault();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonText: 'OK'
                    });
                    
                    return false;
                }
                
                // Konfirmasi sebelum submit
                e.preventDefault();
                
                Swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin membuat permintaan ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#007bff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush