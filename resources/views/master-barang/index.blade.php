{{-- File: resources/views/master-barang/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Master Barang</h2>
                    <p class="text-muted">Kelola data barang dan stok gudang</p>
                </div>
                <div>
                    @if(auth()->user()->isAdminGudang())
                    <a href="{{ route('master-barang.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </a>
                    @endif
                    <a href="{{ route('master-barang.export', request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['stok_habis'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stok Minimum</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['stok_minimum'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kategori</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Umum: {{ $stats['kategori_umum'] }} | Sparepart: {{ $stats['kategori_sparepart'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('master-barang.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari nama/kode barang..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    @if(!auth()->user()->isAdminGudang())
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">Semua</option>
                                <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                                <option value="sparepart" {{ request('kategori') == 'sparepart' ? 'selected' : '' }}>Sparepart</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status Stok</label>
                            <select name="status_stok" class="form-control">
                                <option value="">Semua</option>
                                <option value="normal" {{ request('status_stok') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="minimum" {{ request('status_stok') == 'minimum' ? 'selected' : '' }}>Minimum</option>
                                <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Habis</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Filter Stok</label>
                            <select name="filter_stok" class="form-control">
                                <option value="">Semua</option>
                                <option value="habis" {{ request('filter_stok') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                                <option value="minimum" {{ request('filter_stok') == 'minimum' ? 'selected' : '' }}>Stok Minimum</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Barang --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Barang</h6>
        </div>
        <div class="card-body">
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Stok Tersedia</th>
                            <th>Stok Reserved</th>
                            <th>Stok Aktual</th>
                            <th>Stok Min</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $barang)
                        <tr>
                            <td><strong>{{ $barang->kode_barang }}</strong></td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>
                                <span class="badge badge-{{ $barang->kategori == 'umum' ? 'info' : 'warning' }}">
                                    {{ ucfirst($barang->kategori) }}
                                </span>
                            </td>
                            <td>{{ $barang->satuan }}</td>
                            <td class="text-center">
                                <strong>{{ $barang->stok_tersedia }}</strong>
                            </td>
                            <td class="text-center text-muted">
                                {{ $barang->stok_reserved }}
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $barang->getStokTersediaAktual() <= 0 ? 'danger' : 'success' }}">
                                    {{ $barang->getStokTersediaAktual() }}
                                </strong>
                            </td>
                            <td class="text-center text-muted">
                                {{ $barang->stok_minimum }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $barang->getStatusStokColor() }}">
                                    {{ $barang->getStatusStokLabel() }}
                                </span>
                            </td>
                            <td><small>{{ $barang->lokasi_gudang ?? '-' }}</small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('master-barang.show', $barang) }}" 
                                       class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdminGudang() && auth()->user()->getGudangKategori() == $barang->kategori)
                                    <a href="{{ route('master-barang.edit', $barang) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-success" 
                                            data-toggle="modal" 
                                            data-target="#updateStokModal{{ $barang->id }}"
                                            title="Update Stok">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    @endif
                                </div>

                                {{-- Modal Update Stok --}}
                                @if(auth()->user()->isAdminGudang() && auth()->user()->getGudangKategori() == $barang->kategori)
                                <div class="modal fade" id="updateStokModal{{ $barang->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('master-barang.update-stok', $barang) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Stok - {{ $barang->nama_barang }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        Stok saat ini: <strong>{{ $barang->stok_tersedia }} {{ $barang->satuan }}</strong>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Aksi</label>
                                                        <select name="aksi" class="form-control" required>
                                                            <option value="tambah">Tambah Stok</option>
                                                            <option value="kurangi">Kurangi Stok</option>
                                                            <option value="set">Set Stok (Atur Ulang)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jumlah</label>
                                                        <input type="number" name="jumlah" class="form-control" min="1" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Keterangan (opsional)</label>
                                                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Update Stok</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data barang
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $barangs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection