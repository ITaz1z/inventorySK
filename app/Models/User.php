<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function permintaanHeaders()
    {
        return $this->hasMany(PermintaanHeader::class);
    }

    public function reviewedItems()
    {
        return $this->hasMany(PermintaanItem::class, 'reviewed_by');
    }

    public function activities()
    {
        return $this->hasMany(PermintaanActivity::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchasing_user_id');
    }

    // ==========================================
    // ROLE CHECKER METHODS
    // ==========================================
    
    public function isAdminGudang()
    {
        return in_array($this->role, ['admin_gudang_umum', 'admin_gudang_sparepart']);
    }

    public function isPurchasing()
    {
        return in_array($this->role, ['purchasing_1', 'purchasing_2']);
    }

    public function isManager()
    {
        return in_array($this->role, ['general_manager', 'atasan']);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================
    
    /**
     * Get kategori gudang berdasarkan role user
     */
    public function getGudangKategori()
    {
        if ($this->role === 'admin_gudang_umum') {
            return 'umum';
        }
        
        if ($this->role === 'admin_gudang_sparepart') {
            return 'sparepart';
        }
        
        return null;
    }

    /**
     * Get role label/display name
     */
    public function getRoleLabel()
    {
        $roleLabels = [
            'admin_gudang_umum' => 'Admin Gudang Umum',
            'admin_gudang_sparepart' => 'Admin Gudang Sparepart',
            'purchasing_1' => 'Purchasing 1',
            'purchasing_2' => 'Purchasing 2',
            'general_manager' => 'General Manager',
            'atasan' => 'Atasan/Direktur',
        ];

        return $roleLabels[$this->role] ?? ucwords(str_replace('_', ' ', $this->role));
    }

    /**
     * Check apakah user bisa mengakses kategori barang tertentu
     */
    public function canAccessKategori($kategori)
    {
        if ($this->isAdminGudang()) {
            return $this->getGudangKategori() === $kategori;
        }
        
        if ($this->isPurchasing() || $this->isManager()) {
            return true;
        }
        
        return false;
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeForUser($query, $user)
    {
        if ($user->isAdminGudang()) {
            return $query->where('user_id', $user->id);
        }
        
        return $query;
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdminGudang($query)
    {
        return $query->whereIn('role', ['admin_gudang_umum', 'admin_gudang_sparepart']);
    }

    public function scopePurchasing($query)
    {
        return $query->whereIn('role', ['purchasing_1', 'purchasing_2']);
    }

    public function scopeManager($query)
    {
        return $query->whereIn('role', ['general_manager', 'atasan']);
    }
}