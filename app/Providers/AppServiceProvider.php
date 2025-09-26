<?php
// File: app/Providers/AuthServiceProvider.php
// Updated untuk menambahkan Gates baru

namespace App\Providers;

use App\Models\User;
use App\Models\PermintaanBarang; // Model lama
use App\Models\PermintaanHeader; // Model baru
use App\Models\PermintaanItem;   // Model baru
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // ========================================
        // BASIC ROLE GATES
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
        // OLD PERMINTAAN GATES (Backward Compatibility)
        // ========================================
        
        Gate::define('review-permintaan', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('create-permintaan', function (User $user) {
            return $user->isAdminGudang();
        });
        
        Gate::define('view-permintaan', function (User $user, PermintaanBarang $permintaan) {
            if ($user->isAdminGudang()) {
                return $permintaan->user_id === $user->id;
            }
            
            return $user->isPurchasing() || $user->isManager();
        });
        
        Gate::define('edit-permintaan', function (User $user, PermintaanBarang $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->status === 'pending';
        });
        
        // ========================================
        // NEW PERMINTAAN HEADER GATES
        // ========================================
        
        Gate::define('create-permintaan-header', function (User $user) {
            return $user->isAdminGudang();
        });
        
        Gate::define('view-permintaan-header', function (User $user, PermintaanHeader $permintaan) {
            // Admin gudang hanya bisa lihat miliknya sendiri
            if ($user->isAdminGudang()) {
                return $permintaan->user_id === $user->id;
            }
            
            // Purchasing dan Manager bisa lihat semua
            return $user->isPurchasing() || $user->isManager();
        });
        
        Gate::define('edit-permintaan-header', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->canBeEdited();
        });
        
        Gate::define('delete-permintaan-header', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->isDraft();
        });
        
        Gate::define('submit-permintaan-header', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->isDraft()
                && $permintaan->items()->count() > 0;
        });
        
        Gate::define('review-permintaan-header', function (User $user, PermintaanHeader $permintaan) {
            return $user->isPurchasing() && $permintaan->isPending();
        });
        
        // ========================================
        // PERMINTAAN ITEM GATES
        // ========================================
        
        Gate::define('create-permintaan-item', function (User $user, PermintaanHeader $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->canBeEdited();
        });
        
        Gate::define('edit-permintaan-item', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && $item->header->canBeEdited();
        });
        
        Gate::define('delete-permintaan-item', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && $item->header->canBeEdited();
        });
        
        Gate::define('review-permintaan-item', function (User $user, PermintaanItem $item) {
            return $user->isPurchasing() && $item->isPending();
        });
        
        // ========================================
        // PURCHASE ORDER GATES
        // ========================================
        
        Gate::define('send-purchase-order', function (User $user) {
            return $user->isPurchasing();
        });
        
        Gate::define('view-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            // Purchasing hanya bisa lihat miliknya
            if ($user->isPurchasing()) {
                return $purchaseOrder->purchasing_user_id === $user->id;
            }
            
            // Manager bisa lihat semua
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
        // COMBINATION GATES (untuk complex permissions)
        // ========================================
        
        // Gate untuk melihat semua permintaan (dashboard manager/purchasing)
        Gate::define('view-all-permintaan', function (User $user) {
            return $user->isPurchasing() || $user->isManager();
        });
        
        // Gate untuk export/report
        Gate::define('export-permintaan', function (User $user) {
            return $user->isManager();
        });
        
        // Gate untuk approve/reject batch
        Gate::define('batch-review-permintaan', function (User $user) {
            return $user->isPurchasing();
        });
        
        // Gate untuk setting prioritas urgent
        Gate::define('set-urgent-priority', function (User $user, PermintaanHeader $permintaan) {
            // Manager bisa set urgent kapan saja
            if ($user->isManager()) {
                return true;
            }
            
            // Admin gudang hanya bisa set urgent untuk miliknya yang masih draft/pending
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && in_array($permintaan->status, ['draft', 'pending']);
        });
        
        // ========================================
        // FILE UPLOAD GATES
        // ========================================
        
        Gate::define('upload-item-image', function (User $user, PermintaanItem $item) {
            return $user->isAdminGudang() 
                && $item->header->user_id === $user->id 
                && $item->header->canBeEdited();
        });
        
        // ========================================
        // NOTIFICATION GATES
        // ========================================
        
        Gate::define('receive-permintaan-notifications', function (User $user) {
            return $user->isPurchasing() || $user->isManager();
        });
        
        Gate::define('receive-po-notifications', function (User $user) {
            return $user->isManager();
        });
    }
}