@extends('layouts.app')

@section('title', 'Buat Permintaan Barang')
@section('page-title', 'Buat Permintaan Barang')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i>
                    <h5 class="mb-0">Form Permintaan Barang Baru</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Info Alert -->
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Informasi:</strong>
                            @if(Auth::user()->role === 'admin_gudang_umum')
                                Anda akan membuat permintaan untuk kategori <strong>Umum</strong> (ATK, peralatan kantor, bahan habis pakai, dll.)
                            @else
                                Anda akan membuat permintaan untuk kategori <strong>Sparepart</strong> (suku cadang, komponen mesin, dll.)
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('permintaan.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Nama Barang -->
                        <div class="col-12 mb-4">
                            <label for="nama_barang" class="form-label fw-semibold">
                                <i class="fas fa-box text-primary me-2"></i>Nama Barang <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('nama_barang') is-invalid @enderror" 
                                   id="nama_barang" 
                                   name="nama_barang" 
                                   value="{{ old('nama_barang') }}"
                                   placeholder="Masukkan nama barang yang dibutuhkan"
                                   required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Contoh barang -->
                            <div class="form-text">
                                <strong>Contoh:</strong>
                                @if(Auth::user()->role === 'admin_gudang_umum')
                                    Kertas A4, Tinta Printer, Lem Kertas, Stapler, dll.
                                @else
                                    Bearing Motor, Filter Udara, V-Belt, Sensor Suhu, dll.
                                @endif
                            </div>
                        </div>
                        
                        <!-- Jumlah & Satuan -->
                        <div class="col-md-6 mb-4">
                            <label for="jumlah" class="form-label fw-semibold">
                                <i class="fas fa-sort-numeric-up text-primary me-2"></i>Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg @error('jumlah') is-invalid @enderror" 
                                   id="jumlah" 
                                   name="jumlah" 
                                   value="{{ old('jumlah') }}"
                                   min="1"
                                   placeholder="0"
                                   required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="satuan" class="form-label fw-semibold">
                                <i class="fas fa-balance-scale text-primary me-2"></i>Satuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('satuan') is-invalid @enderror" 
                                    id="satuan" 
                                    name="satuan" 
                                    required>
                                <option value="">Pilih Satuan</option>
                                <optgroup label="Umum">
                                    <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="set" {{ old('satuan') == 'set' ? 'selected' : '' }}>Set</option>
                                    <option value="unit" {{ old('satuan') == 'unit' ? 'selected' : '' }}>Unit</option>
                                    <option value="buah" {{ old('satuan') == 'buah' ? 'selected' : '' }}>Buah</option>
                                </optgroup>
                                <optgroup label="Volume">
                                    <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="ml" {{ old('satuan') == 'ml' ? 'selected' : '' }}>Mililiter</option>
                                    <option value="galon" {{ old('satuan') == 'galon' ? 'selected' : '' }}>Galon</option>
                                </optgroup>
                                <optgroup label="Berat">
                                    <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                    <option value="gram" {{ old('satuan') == 'gram' ? 'selected' : '' }}>Gram</option>
                                    <option value="ton" {{ old('satuan') == 'ton' ? 'selected' : '' }}>Ton</option>
                                </optgroup>
                                <optgroup label="Panjang">
                                    <option value="meter" {{ old('satuan') == 'meter' ? 'selected' : '' }}>Meter</option>
                                    <option value="cm" {{ old('satuan') == 'cm' ? 'selected' : '' }}>Centimeter</option>
                                    <option value="mm" {{ old('satuan') == 'mm' ? 'selected' : '' }}>Milimeter</option>
                                </optgroup>
                                <optgroup label="Kemasan">
                                    <option value="kotak" {{ old('satuan') == 'kotak' ? 'selected' : '' }}>Kotak</option>
                                    <option value="dus" {{ old('satuan') == 'dus' ? 'selected' : '' }}>Dus</option>
                                    <option value="pack" {{ old('satuan') == 'pack' ? 'selected' : '' }}>Pack</option>
                                    <option value="roll" {{ old('satuan') == 'roll' ? 'selected' : '' }}>Roll</option>
                                </optgroup>
                            </select>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tanggal Butuh -->
                        <div class="col-md-6 mb-4">
                            <label for="tanggal_butuh" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Tanggal Dibutuhkan
                            </label>
                            <input type="date" 
                                   class="form-control form-control-lg @error('tanggal_butuh') is-invalid @enderror" 
                                   id="tanggal_butuh" 
                                   name="tanggal_butuh" 
                                   value="{{ old('tanggal_butuh') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('tanggal_butuh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional - Jika ada deadline khusus</div>
                        </div>
                        
                        <!-- Priority Level -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-exclamation-triangle text-primary me-2"></i>Tingkat Prioritas
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityNormal" value="normal" checked>
                                    <label class="form-check-label" for="priorityNormal">
                                        <span class="badge bg-success">Normal</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityUrgent" value="urgent">
                                    <label class="form-check-label" for="priorityUrgent">
                                        <span class="badge bg-warning">Urgent</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityCritical" value="critical">
                                    <label class="form-check-label" for="priorityCritical">
                                        <span class="badge bg-danger">Critical</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Keterangan -->
                        <div class="col-12 mb-4">
                            <label for="keterangan" class="form-label fw-semibold">
                                <i class="fas fa-comment text-primary me-2"></i>Keterangan
                            </label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="4"
                                      placeholder="Jelaskan detail kebutuhan, spesifikasi, atau informasi tambahan lainnya...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Berikan detail spesifikasi atau kegunaan barang</div>
                        </div>
                    </div>
                    
                    <!-- Summary Card -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Ringkasan Permintaan
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Pemohon:</small>
                                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Kategori:</small>
                                    <div class="fw-semibold">
                                        <span class="badge {{ Auth::user()->role === 'admin_gudang_umum' ? 'bg-primary' : 'bg-warning' }}">
                                            {{ Auth::user()->role === 'admin_gudang_umum' ? 'Umum' : 'Sparepart' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                      <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Kirim Permintaan
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-suggest berdasarkan kategori
    document.getElementById('nama_barang').addEventListener('input', function() {
        // You can implement auto-suggest functionality here
        const value = this.value.toLowerCase();
        
        // Example suggestions based on role
        const suggestions = @if(Auth::user()->role === 'admin_gudang_umum')
            ['Kertas A4', 'Tinta Printer', 'Lem Kertas', 'Stapler', 'Penggaris', 'Spidol', 'Correction Pen']
        @else
            ['Bearing Motor', 'Filter Udara', 'V-Belt', 'Sensor Suhu', 'Oil Seal', 'Gasket', 'Switch']
        @endif;
        
        // Simple suggestion implementation (you can enhance this)
    });
</script>
@endpush