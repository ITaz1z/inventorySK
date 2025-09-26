<?php
// File: app/Models/User.php
// Update model User untuk menambahkan relationship baru

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

    // OLD Relationships (masih dipertahankan untuk backward compatibility)
    public function permintaanBarangs()
    {
        return $this->hasMany(PermintaanBarang::class);
    }

    // NEW Relationships untuk struktur baru
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

    // User (purchasing) punya banyak PO
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchasing_user_id');
    }

    // Helper method untuk cek role
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

    // Helper untuk mendapatkan kategori gudang admin
    public function getGudangKategori()
    {
        if ($this->role === 'admin_gudang_sparepart') {
            return 'sparepart';
        }
        
        if ($this->role === 'admin_gudang_umum') {
            return 'umum';
        }
        
        return null;
    }

    // Helper untuk mendapatkan label role
    public function getRoleLabel()
    {
        return [
            'admin_gudang_umum' => 'Admin Gudang Umum',
            'admin_gudang_sparepart' => 'Admin Gudang Sparepart',
            'purchasing_1' => 'Purchasing 1',
            'purchasing_2' => 'Purchasing 2',
            'general_manager' => 'General Manager',
            'atasan' => 'Atasan'
        ][$this->role] ?? $this->role;
    }

    // Scope untuk mendapatkan user berdasarkan role
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