<?php
// File: app/Http/Controllers/MasterBarangController.php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterBarangController extends Controller
{
    // List semua barang dengan filter
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = MasterBarang::with(['createdBy', 'updatedBy']);
        
        // Filter berdasarkan kategori user (admin gudang hanya lihat kategorinya)
        if ($user->isAdminGudang()) {
            $kategori = $user->getGudangKategori();
            $query->where('kategori', $kategori);
        }
        
        // Filter dari request
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->filled('status_stok')) {
            $query->where('status_stok', $request->status_stok);
        }
        
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Filter stok khusus
        if ($request->filled('filter_stok')) {
            switch ($request->filter_stok) {
                case 'habis':
                    $query->stokHabis();
                    break;
                case 'minimum':
                    $query->stokRendah();
                    break;
            }
        }
        
        $barangs = $query->orderBy('nama_barang', 'asc')->paginate(15);
        
        // Stats untuk dashboard
        $stats = [
            'total' => MasterBarang::count(),
            'stok_habis' => MasterBarang::stokHabis()->count(),
            'stok_minimum' => MasterBarang::stokRendah()->count(),
            'kategori_umum' => MasterBarang::where('kategori', 'umum')->count(),
            'kategori_sparepart' => MasterBarang::where('kategori', 'sparepart')->count(),
        ];
        
        return view('master-barang.index', compact('barangs', 'stats'));
    }
    
    // Form create
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Hanya Admin Gudang yang dapat menambah barang');
        }
        
        return view('master-barang.create');
    }
    
    // Store barang baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'kode_barang' => 'nullable|string|max:50|unique:master_barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'stok_minimum' => 'required|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'lokasi_gudang' => 'nullable|string|max:100',
            'harga_rata_rata' => 'nullable|numeric|min:0',
            'supplier_utama' => 'nullable|string|max:200'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Set kategori berdasarkan role user
            $kategori = $user->getGudangKategori();
            
            $barang = MasterBarang::create(array_merge($validated, [
                'kategori' => $kategori,
                'created_by' => $user->id,
                'is_active' => true
            ]));
            
            // Update status stok
            $barang->updateStatusStok();
            
            DB::commit();
            
            return redirect()->route('master-barang.index')
                ->with('success', "Barang '{$barang->nama_barang}' berhasil ditambahkan");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    // Detail barang
    public function show(MasterBarang $masterBarang)
    {
        $user = Auth::user();
        
        // Validasi akses berdasarkan kategori
        if ($user->isAdminGudang() && $masterBarang->kategori !== $user->getGudangKategori()) {
            abort(403, 'Anda tidak memiliki akses ke barang kategori ini');
        }
        
        $masterBarang->load(['createdBy', 'updatedBy', 'permintaanItems' => function($query) {
            $query->with('header')->latest()->limit(10);
        }]);
        
        return view('master-barang.show', compact('masterBarang'));
    }
    
    // Form edit
    public function edit(MasterBarang $masterBarang)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Hanya Admin Gudang yang dapat mengedit barang');
        }
        
        // Validasi akses berdasarkan kategori
        if ($masterBarang->kategori !== $user->getGudangKategori()) {
            abort(403, 'Anda tidak memiliki akses ke barang kategori ini');
        }
        
        return view('master-barang.edit', compact('masterBarang'));
    }
    
    // Update barang
    public function update(Request $request, MasterBarang $masterBarang)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }
        
        // Validasi akses berdasarkan kategori
        if ($masterBarang->kategori !== $user->getGudangKategori()) {
            abort(403, 'Anda tidak memiliki akses ke barang kategori ini');
        }
        
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:master_barangs,kode_barang,' . $masterBarang->id,
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'stok_minimum' => 'required|integer|min:0',
            'stok_maksimum' => 'nullable|integer|min:0',
            'lokasi_gudang' => 'nullable|string|max:100',
            'harga_rata_rata' => 'nullable|numeric|min:0',
            'supplier_utama' => 'nullable|string|max:200',
            'is_active' => 'boolean'
        ]);
        
        DB::beginTransaction();
        
        try {
            $masterBarang->update(array_merge($validated, [
                'updated_by' => $user->id
            ]));
            
            // Update status stok
            $masterBarang->updateStatusStok();
            
            DB::commit();
            
            return redirect()->route('master-barang.show', $masterBarang)
                ->with('success', 'Data barang berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    // Hapus barang (soft delete - set is_active = false)
    public function destroy(MasterBarang $masterBarang)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }
        
        // Validasi akses berdasarkan kategori
        if ($masterBarang->kategori !== $user->getGudangKategori()) {
            abort(403, 'Anda tidak memiliki akses ke barang kategori ini');
        }
        
        // Cek apakah barang sedang digunakan dalam permintaan aktif
        $activePermintaan = $masterBarang->permintaanItems()
            ->whereHas('header', function($q) {
                $q->whereIn('status', ['draft', 'pending', 'review']);
            })->count();
        
        if ($activePermintaan > 0) {
            return back()->withErrors([
                'error' => 'Barang tidak dapat dihapus karena masih digunakan dalam ' . $activePermintaan . ' permintaan aktif'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Soft delete - set is_active = false
            $masterBarang->update([
                'is_active' => false,
                'updated_by' => $user->id
            ]);
            
            DB::commit();
            
            return redirect()->route('master-barang.index')
                ->with('success', "Barang '{$masterBarang->nama_barang}' berhasil dinonaktifkan");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    
    // Update stok manual
    public function updateStok(Request $request, MasterBarang $masterBarang)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }
        
        // Validasi akses berdasarkan kategori
        if ($masterBarang->kategori !== $user->getGudangKategori()) {
            abort(403, 'Anda tidak memiliki akses ke barang kategori ini');
        }
        
        $validated = $request->validate([
            'aksi' => 'required|in:tambah,kurangi,set',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldStok = $masterBarang->stok_tersedia;
            
            switch ($validated['aksi']) {
                case 'tambah':
                    $masterBarang->tambahStok($validated['jumlah']);
                    $message = "Stok ditambah {$validated['jumlah']} {$masterBarang->satuan}";
                    break;
                    
                case 'kurangi':
                    $masterBarang->kurangiStok($validated['jumlah']);
                    $message = "Stok dikurangi {$validated['jumlah']} {$masterBarang->satuan}";
                    break;
                    
                case 'set':
                    $masterBarang->update(['stok_tersedia' => $validated['jumlah']]);
                    $masterBarang->updateStatusStok();
                    $message = "Stok diset menjadi {$validated['jumlah']} {$masterBarang->satuan}";
                    break;
            }
            
            // Log perubahan stok (bisa dibuat tabel terpisah untuk audit)
            \Log::info("Stok Update - {$masterBarang->kode_barang}", [
                'user_id' => $user->id,
                'aksi' => $validated['aksi'],
                'stok_sebelum' => $oldStok,
                'stok_sesudah' => $masterBarang->fresh()->stok_tersedia,
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan']
            ]);
            
            DB::commit();
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    // Export to Excel/CSV (optional)
    public function export(Request $request)
    {
        $user = Auth::user();
        
        $query = MasterBarang::query();
        
        if ($user->isAdminGudang()) {
            $kategori = $user->getGudangKategori();
            $query->where('kategori', $kategori);
        }
        
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $barangs = $query->orderBy('nama_barang')->get();
        
        $filename = 'master-barang-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $columns = [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok Tersedia',
            'Stok Reserved',
            'Stok Aktual',
            'Stok Minimum',
            'Status Stok',
            'Lokasi Gudang'
        ];
        
        $callback = function() use ($barangs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($barangs as $barang) {
                fputcsv($file, [
                    $barang->kode_barang,
                    $barang->nama_barang,
                    ucfirst($barang->kategori),
                    $barang->satuan,
                    $barang->stok_tersedia,
                    $barang->stok_reserved,
                    $barang->getStokTersediaAktual(),
                    $barang->stok_minimum,
                    $barang->getStatusStokLabel(),
                    $barang->lokasi_gudang
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}