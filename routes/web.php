<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\PurchaseOrderController;

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
    
    // Permintaan Barang routes
    Route::resource('permintaan', PermintaanBarangController::class);
    
    // Review permintaan (hanya untuk purchasing)
    Route::post('permintaan/{permintaan}/review', [PermintaanBarangController::class, 'review'])
        ->name('permintaan.review')
        ->middleware('can:review-permintaan');
    
    // Purchase Order routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    
    // Send PO (hanya untuk purchasing)
    Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])
        ->name('purchase-orders.send')
        ->middleware('can:send-purchase-order');
    
});