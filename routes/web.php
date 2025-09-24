<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\PurchaseOrderController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Redirect home ke dashboard
Route::get('/home', function() {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Permintaan Barang routes
    Route::resource('permintaan', PermintaanBarangController::class);
    Route::post('permintaan/{permintaan}/review', [PermintaanBarangController::class, 'review'])->name('permintaan.review');
    
    // Purchase Order routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
});