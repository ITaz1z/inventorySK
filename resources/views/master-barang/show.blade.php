{{-- File: resources/views/master-barang/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $masterBarang->nama_barang }}</h2>
                    <p class="text-muted">{{ $masterBarang->kode_barang }}</p>
                </div>
                <div>
                    @if(auth()->user()->isAdminGudang() && auth()->user()->getGudangKategori() == $masterBarang->kategori)
                    <a href="{{ route('master-barang.edit', $masterBarang) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#updateStokModal">
                        <i class="fas fa-sync"></i> Update Stok
                    </button>
                    @endif
                    <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <div class="row">
        {{-- Informasi Barang --}}
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Barang</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Kode Barang</th>
                            <td><strong>{{ $masterBarang->kode_barang }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td>{{ $masterBarang->nama_barang }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                <span class="badge badge-{{ $masterBarang->kategori == 'umum' ? 'info' : 'warning' }}">
                                    {{ ucfirst($masterBarang->kategori) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $masterBarang->satuan }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $masterBarang->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Gudang</th>
                            <td>
                                @if($masterBarang->lokasi_gudang)
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $masterBarang->lokasi_gudang }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Harga Rata-rata</th>
                            <td>
                                @if($masterBarang->harga_rata_rata)
                                    Rp {{ number_format($masterBarang->harga_rata_rata, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Supplier Utama</th>
                            <td>{{ $masterBarang->supplier_utama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-{{ $masterBarang->is_active ? 'success' : 'secondary' }}">
                                    {{ $masterBarang->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Audit Trail</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th>Dibuat oleh</th>
                            <td>{{ $masterBarang->createdBy->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal dibuat</th>
                            <td>{{ $masterBarang->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate oleh</th>
                            <td>{{ $masterBarang->updatedBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir update</th>
                            <td>{{ $masterBarang->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Informasi Stok --}}
        <div class="col-md-6">
            <div class="card shadow mb-4 border-left-{{ $masterBarang->getStatusStokColor() }}">
                <div class="card-header py-3 bg-{{ $masterBarang->getStatusStokColor() }} text-white">
                    <h6 class="m-0 font-weight-bold">Status Stok</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="display-3 text-{{ $masterBarang->getStatusStokColor() }}">
                            {{ $masterBarang->getStokTersediaAktual() }}
                        </h1>
                        <h5 class="text-muted">{{ $masterBarang->satuan }} tersedia</h5>
                        <span class="badge badge-{{ $masterBarang->getStatusStokColor() }} badge-lg p-2">
                            {{ $masterBarang->getStatusStokLabel() }}
                        </span>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-right">
                                <h4 class="text-success">{{ $masterBarang->stok_tersedia }}</h4>
                                <small class="text-muted">Stok Fisik</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-right">
                                <h4 class="text-warning">{{ $masterBarang->stok_reserved }}</h4>
                                <small class="text-muted">Reserved</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger">{{ $masterBarang->stok_minimum }}</h4>
                            <small class="text-muted">Stok Min</small>
                        </div>
                    </div>

                    <hr>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><i class="fas fa-box text-success"></i> Stok Tersedia</td>
                            <td class="text-right"><strong>{{ $masterBarang->stok_tersedia }} {{ $masterBarang->satuan }}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-lock text-warning"></i> Stok Reserved</td>
                            <td class="text-right"><strong>{{ $masterBarang->stok_reserved }} {{ $masterBarang->satuan }}</strong></td>
                        </tr>
                        <tr class="border-top">
                            <td><i class="fas fa-check-circle text-primary"></i> <strong>Stok Aktual</strong></td>
                            <td class="text-right">
                                <strong class="text-{{ $masterBarang->getStokTersediaAktual() <= 0 ? 'danger' : 'success' }}">
                                    {{ $masterBarang->getStokTersediaAktual() }} {{ $masterBarang->satuan }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-exclamation-triangle text-danger"></i> Batas Minimum</td>
                            <td class="text-right"><strong>{{ $masterBarang->stok_minimum }} {{ $masterBarang->satuan }}</strong></td>
                        </tr>
                        @if($masterBarang->stok_maksimum)
                        <tr>
                            <td><i class="fas fa-layer-group text-info"></i> Batas Maksimum</td>
                            <td class="text-right"><strong>{{ $masterBarang->stok_maksimum }} {{ $masterBarang->satuan }}</strong></td>
                        </tr>
                        @endif
                    </table>

                    @if($masterBarang->isStokHabis())
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle"></i> <strong>Stok Habis!</strong> Segera lakukan restock.
                    </div>
                    @elseif($masterBarang->isStokMinimum())
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Stok Minimum!</strong> Pertimbangkan untuk restock.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Permintaan --}}
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Permintaan (10 Terakhir)</h6>
        </div>
        <div class="card-body">
            @if($masterBarang->permintaanItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Permintaan</th>
                            <th>Diminta Oleh</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal Dibutuhkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterBarang->permintaanItems as $item)
                        <tr>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('permintaan.show', $item->header) }}">
                                    {{ $item->header->nomor_permintaan }}
                                </a>
                            </td>
                            <td>{{ $item->header->user->name }}</td>
                            <td>{{ $item->jumlah }} {{ $item->satuan }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $item->status == 'approved' ? 'success' : 
                                    ($item->status == 'rejected' ? 'danger' : 
                                    ($item->status == 'partial' ? 'warning' : 'secondary')) 
                                }}">
                                    {{ $item->getStatusLabel() }}
                                </span>
                            </td>
                            <td>{{ $item->header->tanggal_dibutuhkan->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                Belum ada riwayat permintaan untuk barang ini
            </p>
            @endif
        </div>
    </div>
</div>

{{-- Modal Update Stok --}}
@if(auth()->user()->isAdminGudang() && auth()->user()->getGudangKategori() == $masterBarang->kategori)
<div class="modal fade" id="updateStokModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('master-barang.update-stok', $masterBarang) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Stok - {{ $masterBarang->nama_barang }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Stok saat ini:</strong><br>
                        • Fisik: {{ $masterBarang->stok_tersedia }} {{ $masterBarang->satuan }}<br>
                        • Reserved: {{ $masterBarang->stok_reserved }} {{ $masterBarang->satuan }}<br>
                        • Aktual: {{ $masterBarang->getStokTersediaAktual() }} {{ $masterBarang->satuan }}
                    </div>
                    <div class="form-group">
                        <label>Aksi <span class="text-danger">*</span></label>
                        <select name="aksi" class="form-control" required>
                            <option value="tambah">Tambah Stok (Stok Masuk)</option>
                            <option value="kurangi">Kurangi Stok (Stok Keluar)</option>
                            <option value="set">Set Stok (Atur Ulang/Koreksi)</option>
                        </select>
                        <small class="form-text text-muted">
                            Pilih "Set Stok" untuk koreksi atau stock opname
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah" class="form-control" min="1" required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ $masterBarang->satuan }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" 
                                  placeholder="Catatan: penerimaan dari supplier, stock opname, dll"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection