<?php
// File: app/Models/PermintaanHeader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_permintaan',
        'judul_permintaan',
        'tanggal_permintaan',
        'tanggal_dibutuhkan',
        'tingkat_prioritas',
        'status',
        'catatan_permintaan',
        'total_items',
        'approved_items',
        'rejected_items',
        'submitted_at',
        'reviewed_at',
        'completed_at'
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'tanggal_dibutuhkan' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_items' => 'integer',
        'approved_items' => 'integer',
        'rejected_items' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PermintaanItem::class);
    }

    public function activities()
    {
        return $this->hasMany(PermintaanActivity::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'permintaan_header_id');
    }

    // Helper Methods
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    public function getProgressPercentage()
    {
        if ($this->total_items == 0) return 0;
        return round(($this->approved_items / $this->total_items) * 100);
    }

    public function getPriorityLabel()
    {
        return [
            'urgent' => 'Sangat Urgent',
            'penting' => 'Penting',
            'routine' => 'Rutin',
            'non_routine' => 'Non Rutin'
        ][$this->tingkat_prioritas] ?? $this->tingkat_prioritas;
    }

    public function getStatusLabel()
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Menunggu Review',
            'review' => 'Sedang Review',
            'partial' => 'Sebagian Disetujui',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'po_created' => 'PO Dibuat'
        ][$this->status] ?? $this->status;
    }

    // Auto generate nomor permintaan
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($header) {
            if (empty($header->nomor_permintaan)) {
                $header->nomor_permintaan = 'REQ-' . date('Ymd') . '-' . 
                    str_pad((static::whereDate('created_at', today())->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Scope untuk filter berdasarkan role
    public function scopeForUser($query, $user)
    {
        if ($user->isAdminGudang()) {
            return $query->where('user_id', $user->id);
        }
        
        return $query; // Manager dan Purchasing bisa lihat semua
    }
}