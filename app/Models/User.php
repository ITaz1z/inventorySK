<?php
// File: app/Models/User.php

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

    // Relationship: User punya banyak permintaan barang
    public function permintaanBarangs()
    {
        return $this->hasMany(PermintaanBarang::class);
    }

    // Relationship: User (purchasing) punya banyak PO
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
}