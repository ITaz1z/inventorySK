<?php
// File: app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\User;
use App\Models\PermintaanHeader;
use App\Models\PermintaanItem;
use App\Models\PurchaseOrder;
use App\Models\MasterBarang;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // ========================================
        // PERMINTAAN HEADER GATES
        // ========================================
        
        Gate::define('create-permintaan', function (User $user) {
            return $user->isAdminGudang();
        });
        
        Gate::define('view-permintaan', function (User $user, PermintaanHeader $permintaan) {
            if ($user->isAdminGudang()) {
                return $permintaan->user_id === $user->id;
            }
            return $user->isPurchasing() || $user->isManager();
        });
        
        Gate::define('edit-permintaan', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && in_array($permintaan->status, ['draft', 'pending']);
        });
        
        Gate::define('delete-permintaan', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->status === 'draft';
        });
        
        Gate::define('submit-permintaan', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->status === 'draft';
        });
        
        // ========================================
        // PERMINTAAN ITEM GATES
        // ========================================
        
        Gate::define('create-item', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && in_array($permintaan->status, ['draft', 'pending']);
        });
        
        Gate::define('edit-item', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && in_array($item->header->status, ['draft', 'pending']);
        });
        
        Gate::define('delete-item', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && in_array($item->header->status, ['draft', 'pending']);
        });
        
        Gate::define('upload-image', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && in_array($item->header->status, ['draft', 'pending']);
        });
        
        // ========================================
        // REVIEW GATES (PURCHASING)
        // ========================================
        
        Gate::define('review-permintaan', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('review-item', function (User $user, PermintaanItem $item) {
            return $user->isPurchasing() && $item->status === 'pending';
        });
        
        // ========================================
        // PURCHASE ORDER GATES
        // ========================================
        
        Gate::define('create-purchase-order', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('send-purchase-order', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('view-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            if ($user->isPurchasing()) {
                return $purchaseOrder->purchasing_user_id === $user->id;
            }
            return $user->isManager();
        });
        
        Gate::define('edit-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            return $user->isPurchasing() 
                && $purchaseOrder->purchasing_user_id === $user->id 
                && $purchaseOrder->status === 'draft';
        });
        
        Gate::define('delete-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            return $user->isPurchasing() 
                && $purchaseOrder->purchasing_user_id === $user->id 
                && $purchaseOrder->status === 'draft';
        });
        
        // ========================================
        // MASTER BARANG GATES (BARU)
        // ========================================
        
        // SEMUA USER BISA VIEW
        Gate::define('view-master-barang', function (User $user) {
            return true; // Semua authenticated user
        });
        
        // Hanya Admin Gudang yang bisa create
        Gate::define('create-master-barang', function (User $user) {
            return $user->isAdminGudang();
        });
        
        // Edit barang - sesuai kategori
        Gate::define('edit-master-barang', function (User $user, MasterBarang $masterBarang) {
            if (!$user->isAdminGudang()) {
                return false;
            }
            
            $kategoriUser = $user->getGudangKategori();
            return $kategoriUser === $masterBarang->kategori;
        });
        
        // Update stok - sesuai kategori
        Gate::define('update-stok', function (User $user, MasterBarang $masterBarang) {
            if (!$user->isAdminGudang()) {
                return false;
            }
            
            $kategoriUser = $user->getGudangKategori();
            return $kategoriUser === $masterBarang->kategori;
        });
        
        // Delete barang - sesuai kategori
        Gate::define('delete-master-barang', function (User $user, MasterBarang $masterBarang) {
            if (!$user->isAdminGudang()) {
                return false;
            }
            
            $kategoriUser = $user->getGudangKategori();
            return $kategoriUser === $masterBarang->kategori;
        });
        
        // ========================================
        // ROLE-BASED GATES (UNTUK MIDDLEWARE)
        // ========================================
        
        Gate::define('admin-gudang-only', function (User $user) {
            return $user->isAdminGudang();
        });
        
        Gate::define('purchasing-only', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('manager-only', function (User $user) {
            return $user->isManager();
        });
        
        // ========================================
        // VIEW ACCESS GATES
        // ========================================
        
        Gate::define('view-all-permintaan', function (User $user) {
            return $user->isPurchasing() || $user->isManager();
        });
        
        Gate::define('view-all-purchase-orders', function (User $user) {
            return $user->isManager();
        });
        
        // ========================================
        // EXPORT GATES
        // ========================================
        
        Gate::define('export-data', function (User $user) {
            return true; // Semua user bisa export
        });
    }
}