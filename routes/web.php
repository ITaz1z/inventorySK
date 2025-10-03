<?php
// File: routes/web.php (Updated - tambahan route untuk Master Barang)

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PermintaanItemController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\MasterBarangController;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Auth::routes();

// Redirect /home ke dashboard untuk user yang sudah login
Route::get('/home', function() {
    return redirect()->route('dashboard');
})->middleware('auth');

// Protected routes - harus login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tambahkan di dalam Route::middleware(['auth'])->group(function () {

    // ========================================
    // MASTER BARANG ROUTES
    // ========================================
    Route::resource('master-barang', MasterBarangController::class);

    // Update stok manual
    Route::post('master-barang/{masterBarang}/update-stok', [MasterBarangController::class, 'updateStok'])
        ->name('master-barang.update-stok');

    // Export barang
    Route::get('master-barang-export', [MasterBarangController::class, 'export'])
        ->name('master-barang.export');
    // ========================================
    // PERMINTAAN ROUTES (New Structure)
    // ========================================
    
    // Permintaan Header routes
    Route::resource('permintaan', PermintaanController::class);
    
    // Submit permintaan (draft -> pending)
    Route::post('permintaan/{permintaan}/submit', [PermintaanController::class, 'submit'])
        ->name('permintaan.submit');
    
    // Review permintaan (untuk purchasing)
    Route::post('permintaan/{permintaan}/review', [PermintaanController::class, 'review'])
        ->name('permintaan.review')
        ->middleware('can:purchasing-only');
        
    // ========================================
    // PERMINTAAN ITEMS ROUTES (nested)
    // ========================================
    Route::prefix('permintaan/{permintaan}/items')->name('permintaan.items.')->group(function () {
        Route::post('/', [PermintaanItemController::class, 'store'])->name('store');
        Route::put('/{item}', [PermintaanItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [PermintaanItemController::class, 'destroy'])->name('destroy');
        
        // Review item individual
        Route::post('/{item}/review', [PermintaanItemController::class, 'review'])
            ->name('review')
            ->middleware('can:purchasing-only');
        
        // Update order (drag & drop)
        Route::post('/update-order', [PermintaanItemController::class, 'updateOrder'])
            ->name('update-order');
    });
    
    // ========================================
    // PURCHASE ORDER ROUTES
    // ========================================
    Route::resource('purchase-orders', PurchaseOrderController::class);
    
    // Send PO (hanya untuk purchasing)
    Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])
        ->name('purchase-orders.send')
        ->middleware('can:send-purchase-order');
    
    // ========================================
    // API ROUTES untuk AJAX
    // ========================================
    Route::prefix('api')->name('api.')->group(function () {
        
        // Get approved items dari permintaan untuk PO
        Route::get('permintaan/{permintaan}/approved-items', function(App\Models\PermintaanHeader $permintaan) {
            return response()->json([
                'items' => $permintaan->items()
                    ->whereIn('status', ['approved', 'partial'])
                    ->with('masterBarang')
                    ->get()
                    ->map(function($item) {
                        return [
                            'id' => $item->id,
                            'nama_barang' => $item->nama_barang,
                            'jumlah_diminta' => $item->jumlah,
                            'jumlah_disetujui' => $item->getApprovedQuantity(),
                            'satuan' => $item->satuan,
                            'kategori' => $item->kategori,
                            'status' => $item->status,
                            'tipe_permintaan' => $item->tipe_permintaan,
                            'master_barang' => $item->masterBarang ? [
                                'kode_barang' => $item->masterBarang->kode_barang,
                                'stok_tersedia' => $item->masterBarang->stok_tersedia
                            ] : null
                        ];
                    })
            ]);
        })->name('permintaan.approved-items');
        
        // Search master barang untuk autocomplete berdasarkan kategori user
        Route::get('master-barang/search', function() {
            $user = auth()->user();
            $query = request('q', '');
            $kategori = $user->getGudangKategori();
            
            $masterBarangs = \App\Models\MasterBarang::active()
                ->byKategori($kategori)
                ->search($query)
                ->limit(10)
                ->get();
            
            return response()->json([
                'results' => $masterBarangs->map(function($barang) {
                    $stokAktual = $barang->getStokTersediaAktual();
                    return [
                        'id' => $barang->id,
                        'kode_barang' => $barang->kode_barang,
                        'nama_barang' => $barang->nama_barang,
                        'satuan' => $barang->satuan,
                        'kategori' => $barang->kategori,
                        'stok_tersedia' => $barang->stok_tersedia,
                        'stok_reserved' => $barang->stok_reserved,
                        'stok_aktual' => $stokAktual,
                        'stok_minimum' => $barang->stok_minimum,
                        'status_stok' => $barang->status_stok,
                        'status_stok_label' => $barang->getStatusStokLabel(),
                        'status_stok_color' => $barang->getStatusStokColor(),
                        'lokasi_gudang' => $barang->lokasi_gudang,
                        'is_stok_mencukupi' => $stokAktual > 0,
                        'display_text' => "{$barang->kode_barang} - {$barang->nama_barang} (Stok: {$stokAktual})"
                    ];
                })
            ]);
        })->name('master-barang.search');
        
        // Get info barang by ID
        Route::get('master-barang/{id}/info', [PermintaanItemController::class, 'getBarangInfo'])
            ->name('master-barang.info');
        
        // Search barang untuk autocomplete (legacy - untuk backward compatibility)
        Route::get('barang/search', function() {
            $query = request('q', '');
            $kategori = request('kategori');
            
            // Data dummy untuk autocomplete - bisa diganti dengan database nanti
            $barang = [
                // Umum
                ['nama' => 'Kertas A4 80gsm', 'kategori' => 'umum', 'satuan' => 'rim'],
                ['nama' => 'Pulpen Biru', 'kategori' => 'umum', 'satuan' => 'pcs'],
                ['nama' => 'Tinta Printer Canon', 'kategori' => 'umum', 'satuan' => 'botol'],
                ['nama' => 'Stapler Besar', 'kategori' => 'umum', 'satuan' => 'pcs'],
                ['nama' => 'Map Plastik', 'kategori' => 'umum', 'satuan' => 'pcs'],
                
                // Sparepart
                ['nama' => 'Filter Udara Mitsubishi', 'kategori' => 'sparepart', 'satuan' => 'pcs'],
                ['nama' => 'Belt Conveyor', 'kategori' => 'sparepart', 'satuan' => 'meter'],
                ['nama' => 'Bearing 6204', 'kategori' => 'sparepart', 'satuan' => 'pcs'],
                ['nama' => 'Oli Mesin SAE 40', 'kategori' => 'sparepart', 'satuan' => 'liter'],
                ['nama' => 'Seal Hydraulic', 'kategori' => 'sparepart', 'satuan' => 'set'],
            ];
            
            // Filter berdasarkan query dan kategori
            $results = collect($barang)->filter(function($item) use ($query, $kategori) {
                $matchQuery = empty($query) || stripos($item['nama'], $query) !== false;
                $matchKategori = empty($kategori) || $item['kategori'] === $kategori;
                return $matchQuery && $matchKategori;
            });
            
            return response()->json([
                'results' => $results->take(10)->values()
            ]);
        })->name('barang.search');
        
        // Get statistics untuk dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('dashboard/activities', [DashboardController::class, 'getRecentActivities'])->name('dashboard.activities');
        
    });
    
    // ========================================
    // UTILITY ROUTES
    // ========================================
    
    // Download template import barang (untuk future feature)
    Route::get('/download/template-barang', function() {
        // Buat CSV template sederhana
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-barang.csv"',
        ];
        
        $columns = ['nama_barang', 'kategori', 'satuan_default'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Contoh Barang', 'umum', 'pcs']);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    })->name('download.template-barang');
    
});

// ========================================
// FALLBACK ROUTES
// ========================================

// Custom error pages
Route::get('/403', function() {
    return view('errors.403', ['message' => 'Anda tidak memiliki akses ke halaman ini']);
})->name('403');

// Fallback untuk route yang tidak ada
Route::fallback(function () {
    return view('errors.404');
});