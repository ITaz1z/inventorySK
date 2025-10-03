{{-- File: resources/views/master-barang/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Edit Barang</h2>
                    <p class="text-muted">{{ $masterBarang->kode_barang }} - {{ $masterBarang->nama_barang }}</p>
                </div>
                <a href="{{ route('master-barang.show', $masterBarang) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Barang</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('master-barang.update', $masterBarang) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    {{-- Informasi Dasar --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Dasar</h5>
                        
                        <div class="form-group">
                            <label>Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" class="form-control" 
                                   value="{{ old('kode_barang', $masterBarang->kode_barang) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" 
                                   value="{{ old('nama_barang', $masterBarang->nama_barang) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <input type="text" class="form-control" 
                                   value="{{ ucfirst($masterBarang->kategori) }}" disabled>
                            <small class="form-text text-muted">Kategori tidak dapat diubah</small>
                        </div>

                        <div class="form-group">
                            <label>Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" 
                                   value="{{ old('satuan', $masterBarang->satuan) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $masterBarang->deskripsi) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Lokasi Gudang</label>
                            <input type="text" name="lokasi_gudang" class="form-control" 
                                   value="{{ old('lokasi_gudang', $masterBarang->lokasi_gudang) }}">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" 
                                       id="is_active" value="1" 
                                       {{ old('is_active', $masterBarang->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Barang Aktif</label>
                            </div>
                            <small class="form-text text-muted">Barang non-aktif tidak akan muncul di form permintaan</small>
                        </div>
                    </div>

                    {{-- Informasi Stok --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Stok</h5>
                        
                        <div class="alert alert-info">
                            <strong>Stok Saat Ini:</strong><br>
                            • Tersedia: {{ $masterBarang->stok_tersedia }} {{ $masterBarang->satuan }}<br>
                            • Reserved: {{ $masterBarang->stok_reserved }} {{ $masterBarang->satuan }}<br>
                            • Aktual: {{ $masterBarang->getStokTersediaAktual() }} {{ $masterBarang->satuan }}<br>
                            <small class="text-muted">Gunakan tombol "Update Stok" untuk mengubah stok tersedia</small>
                        </div>

                        <div class="form-group">
                            <label>Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control" 
                                   value="{{ old('stok_minimum', $masterBarang->stok_minimum) }}" 
                                   min="0" required>
                        </div>

                        <div class="form-group">
                            <label>Stok Maksimum</label>
                            <input type="number" name="stok_maksimum" class="form-control" 
                                   value="{{ old('stok_maksimum', $masterBarang->stok_maksimum) }}" 
                                   min="0">
                        </div>

                        <hr>

                        <h5 class="mb-3">Informasi Tambahan</h5>

                        <div class="form-group">
                            <label>Harga Rata-rata</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="harga_rata_rata" class="form-control" 
                                       value="{{ old('harga_rata_rata', $masterBarang->harga_rata_rata) }}" 
                                       min="0" step="0.01">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Supplier Utama</label>
                            <input type="text" name="supplier_utama" class="form-control" 
                                   value="{{ old('supplier_utama', $masterBarang->supplier_utama) }}">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Barang
                        </button>
                        <a href="{{ route('master-barang.show', $masterBarang) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="button" class="btn btn-danger float-right" 
                                data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Nonaktifkan Barang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Delete Confirmation --}}
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Nonaktifkan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menonaktifkan barang <strong>{{ $masterBarang->nama_barang }}</strong>?</p>
                    <p class="text-muted">Barang yang dinonaktifkan tidak akan muncul dalam form permintaan baru.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('master-barang.destroy', $masterBarang) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Nonaktifkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection