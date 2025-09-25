<?php
// File: app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\User;
use App\Models\PermintaanBarang;
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
        // Gate untuk review permintaan - hanya purchasing
        Gate::define('review-permintaan', function (User $user) {
            return $user->isPurchasing();
        });
        
        // Gate untuk send purchase order - hanya purchasing
        Gate::define('send-purchase-order', function (User $user) {
            return $user->isPurchasing();
        });
        
        // Gate untuk create permintaan - hanya admin gudang
        Gate::define('create-permintaan', function (User $user) {
            return $user->isAdminGudang();
        });
        
        // Gate untuk view permintaan - berbeda-beda per role
        Gate::define('view-permintaan', function (User $user, PermintaanBarang $permintaan) {
            // Admin gudang hanya bisa lihat miliknya
            if ($user->isAdminGudang()) {
                return $permintaan->user_id === $user->id;
            }
            
            // Purchasing dan Manager bisa lihat semua
            return $user->isPurchasing() || $user->isManager();
        });
        
        // Gate untuk edit permintaan - hanya admin gudang pemilik dan status pending
        Gate::define('edit-permintaan', function (User $user, PermintaanBarang $permintaan) {
            return $user->isAdminGudang() 
                && $permintaan->user_id === $user->id 
                && $permintaan->status === 'pending';
        });
        
        // Gate untuk view purchase order
        Gate::define('view-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            // Purchasing hanya bisa lihat miliknya
            if ($user->isPurchasing()) {
                return $purchaseOrder->purchasing_user_id === $user->id;
            }
            
            // Manager bisa lihat semua
            return $user->isManager();
        });
        
        // Gate untuk edit purchase order - hanya purchasing pemilik dan status draft
        Gate::define('edit-purchase-order', function (User $user, PurchaseOrder $purchaseOrder) {
            return $user->isPurchasing() 
                && $purchaseOrder->purchasing_user_id === $user->id 
                && $purchaseOrder->status === 'draft';
        });
    }
}