{{-- File: resources/views/permintaan/create.blade.php --}}
{{-- Form Create Permintaan - UPDATED VERSION WITH DD/MM/YYYY FORMAT --}}
@extends('layouts.app')

@section('title', 'Buat Permintaan Baru')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Simple Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">📋 Buat Permintaan Baru</h3>
                    <p class="text-muted mb-0">Isi informasi dasar permintaan, lalu tambahkan barang di halaman berikutnya</p>
                </div>
                <a href="{{ route('permintaan.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Error Alert --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>⚠️ Periksa kembali:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Form Card --}}
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    
                    <form action="{{ route('permintaan.store') }}" method="POST" id="formPermintaan">
                        @csrf

                        {{-- Info Box Top --}}
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i>
                            <strong>Langkah 1 dari 2:</strong> Isi informasi dasar permintaan ini dulu. 
                            Setelah disimpan, Anda bisa menambahkan barang-barang yang dibutuhkan.
                        </div>

                        {{-- Judul Permintaan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                📝 Judul Permintaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="judul_permintaan" 
                                   class="form-control form-control-lg @error('judul_permintaan') is-invalid @enderror" 
                                   value="{{ old('judul_permintaan') }}"
                                   placeholder="Contoh: Permintaan ATK Bulan Oktober 2025"
                                   required>
                            @error('judul_permintaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Buat judul yang jelas agar mudah dicari nanti</small>
                        </div>

                        <div class="row">
                            {{-- Tanggal Permintaan - INPUT MANUAL --}}
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">
                                    📋 Tanggal Permintaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="tanggal_permintaan" 
                                       id="tanggal_permintaan"
                                       class="form-control datepicker @error('tanggal_permintaan') is-invalid @enderror" 
                                       value="{{ old('tanggal_permintaan', date('d/m/Y')) }}"
                                       placeholder="dd/mm/yyyy"
                                       required>
                                @error('tanggal_permintaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Format: dd/mm/yyyy</small>
                            </div>

                            {{-- Tanggal Dibutuhkan - INPUT MANUAL --}}
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">
                                    📅 Kapan Dibutuhkan? <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="tanggal_dibutuhkan" 
                                       id="tanggal_dibutuhkan"
                                       class="form-control datepicker @error('tanggal_dibutuhkan') is-invalid @enderror" 
                                       value="{{ old('tanggal_dibutuhkan') }}"
                                       placeholder="dd/mm/yyyy"
                                       required>
                                @error('tanggal_dibutuhkan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimal besok (H+1)</small>
                            </div>

                            {{-- Tingkat Prioritas --}}
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">
                                    ⚡ Seberapa Urgent? <span class="text-danger">*</span>
                                </label>
                                <select name="tingkat_prioritas" 
                                        class="form-select @error('tingkat_prioritas') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Prioritas --</option>
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

                        {{-- Catatan (Optional) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                💬 Catatan Tambahan <span class="text-muted">(opsional)</span>
                            </label>
                            <textarea name="catatan_permintaan" 
                                      class="form-control" 
                                      rows="4"
                                      placeholder="Tuliskan alasan, konteks, atau informasi penting lainnya...">{{ old('catatan_permintaan') }}</textarea>
                            <small class="form-text text-muted">Jelaskan kenapa barang ini dibutuhkan</small>
                        </div>

                        {{-- Info Pemohon (Read-only) --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Pemohon:</small>
                                        <strong>{{ auth()->user()->name }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Kategori:</small>
                                        <strong>{{ auth()->user()->role === 'admin_gudang_sparepart' ? 'Sparepart' : 'Umum' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-arrow-right"></i> Lanjut ke Tambah Barang
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            {{-- Help Card --}}
            <div class="card mt-3 border-0 bg-light">
                <div class="card-body">
                    <h6 class="mb-2">💡 Tips:</h6>
                    <ul class="mb-0 small">
                        <li>Satu permintaan bisa berisi banyak barang</li>
                        <li>Barang akan ditambahkan di halaman berikutnya</li>
                        <li>Permintaan masih bisa diedit sebelum disubmit</li>
                        <li>Format tanggal: dd/mm/yyyy (contoh: 31/12/2025)</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<!-- jQuery (jika belum ada) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    // Auto focus
    $('input[name="judul_permintaan"]').focus();
    
    // Initialize Datepicker dengan format dd/mm/yyyy
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        language: 'id'
    });
    
    // Set minimum date untuk tanggal dibutuhkan (besok)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('#tanggal_dibutuhkan').datepicker('setStartDate', tomorrow);
    
    // Visual feedback untuk prioritas
    $('select[name="tingkat_prioritas"]').on('change', function() {
        const val = $(this).val();
        $(this).removeClass('border-danger border-warning border-success border-info');
        
        if (val === 'urgent') {
            $(this).addClass('border-danger');
        } else if (val === 'penting') {
            $(this).addClass('border-warning');
        } else if (val === 'routine') {
            $(this).addClass('border-success');
        } else if (val === 'non_routine') {
            $(this).addClass('border-info');
        }
    });
    
    // Konfirmasi submit dengan validasi tanggal
    $('#formPermintaan').on('submit', function(e) {
        const judul = $('input[name="judul_permintaan"]').val().trim();
        const tanggalPermintaan = $('#tanggal_permintaan').val();
        const tanggalDibutuhkan = $('#tanggal_dibutuhkan').val();
        const prioritas = $('select[name="tingkat_prioritas"]').val();
        
        if (!judul || !tanggalPermintaan || !tanggalDibutuhkan || !prioritas) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: 'Mohon lengkapi semua field yang wajib diisi (bertanda *)',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Validasi format tanggal dd/mm/yyyy
        const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        
        if (!dateRegex.test(tanggalPermintaan) || !dateRegex.test(tanggalDibutuhkan)) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'error',
                title: 'Format Tanggal Salah',
                text: 'Format tanggal harus dd/mm/yyyy (contoh: 31/12/2025)',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Parse tanggal dari format dd/mm/yyyy
        const parseDateDMY = (dateStr) => {
            const parts = dateStr.split('/');
            return new Date(parts[2], parts[1] - 1, parts[0]);
        };
        
        const datePermintaan = parseDateDMY(tanggalPermintaan);
        const dateDibutuhkan = parseDateDMY(tanggalDibutuhkan);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        // Validasi tanggal dibutuhkan harus minimal besok
        if (dateDibutuhkan < tomorrow) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal dibutuhkan harus minimal besok (H+1)',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Validasi tanggal permintaan tidak lebih dari tanggal dibutuhkan
        if (datePermintaan > dateDibutuhkan) {
            e.preventDefault();
            
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal permintaan tidak boleh lebih dari tanggal dibutuhkan',
                confirmButtonText: 'OK'
            });
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .form-control-lg {
        font-size: 1.1rem;
        padding: 0.75rem 1rem;
    }
    
    .form-label.fw-bold {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .card {
        border-radius: 12px;
        border: none;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .form-select.border-danger {
        border-color: #dc3545 !important;
        border-width: 2px;
    }
    
    .form-select.border-warning {
        border-color: #ffc107 !important;
        border-width: 2px;
    }
    
    .form-select.border-success {
        border-color: #28a745 !important;
        border-width: 2px;
    }
    
    .form-select.border-info {
        border-color: #17a2b8 !important;
        border-width: 2px;
    }
    
    .btn-primary {
        background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(180deg, #224abe 0%, #1a3a9e 100%);
        transform: translateY(-1px);
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    
    /* Datepicker customization */
    .datepicker {
        border-radius: 8px;
    }
    
    .datepicker table tr td.active,
    .datepicker table tr td.active:hover {
        background-color: #4e73df !important;
        background-image: none;
    }
    
    .datepicker table tr td.today {
        background-color: #f0f0f0 !important;
    }
</style>
@endpush
@endsection