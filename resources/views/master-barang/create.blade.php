{{-- File: resources/views/master-barang/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Tambah Barang Baru</h2>
                    <p class="text-muted">Kategori: <strong>{{ ucfirst(auth()->user()->getGudangKategori()) }}</strong></p>
                </div>
                <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang</h6>
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

            <form method="POST" action="{{ route('master-barang.store') }}">
                @csrf
                
                <div class="row">
                    {{-- Informasi Dasar --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Dasar</h5>
                        
                        <div class="form-group">
                            <label>Kode Barang <small class="text-muted">(Opsional - auto generate)</small></label>
                            <input type="text" name="kode_barang" class="form-control" 
                                   value="{{ old('kode_barang') }}" 
                                   placeholder="Kosongkan untuk auto generate">
                            <small class="form-text text-muted">
                                Format auto: {{ auth()->user()->getGudangKategori() == 'sparepart' ? 'SP' : 'UM' }}{{ date('y') }}0001
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" 
                                   value="{{ old('nama_barang') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" 
                                   value="{{ old('satuan') }}" 
                                   placeholder="pcs, unit, meter, liter, dll" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Lokasi Gudang</label>
                            <input type="text" name="lokasi_gudang" class="form-control" 
                                   value="{{ old('lokasi_gudang') }}" 
                                   placeholder="Contoh: Rak A1, Zona B, dll">
                        </div>
                    </div>

                    {{-- Informasi Stok --}}
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Stok</h5>
                        
                        <div class="form-group">
                            <label>Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok_tersedia" class="form-control" 
                                   value="{{ old('stok_tersedia', 0) }}" min="0" required>
                            <small class="form-text text-muted">Jumlah stok saat pertama kali input</small>
                        </div>

                        <div class="form-group">
                            <label>Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_minimum" class="form-control" 
                                   value="{{ old('stok_minimum', 0) }}" min="0" required>
                            <small class="form-text text-muted">Batas minimal stok sebelum perlu restock</small>
                        </div>

                        <div class="form-group">
                            <label>Stok Maksimum <small class="text-muted">(Opsional)</small></label>
                            <input type="number" name="stok_maksimum" class="form-control" 
                                   value="{{ old('stok_maksimum') }}" min="0">
                            <small class="form-text text-muted">Batas maksimal kapasitas penyimpanan</small>
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
                                       value="{{ old('harga_rata_rata') }}" min="0" step="0.01">
                            </div>
                            <small class="form-text text-muted">Untuk estimasi nilai inventory</small>
                        </div>

                        <div class="form-group">
                            <label>Supplier Utama</label>
                            <input type="text" name="supplier_utama" class="form-control" 
                                   value="{{ old('supplier_utama') }}" 
                                   placeholder="Nama supplier/vendor">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Barang
                        </button>
                        <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection