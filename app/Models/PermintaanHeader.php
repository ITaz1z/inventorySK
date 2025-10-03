<?php
// File: app/Models/PermintaanHeader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanHeader extends Model
{
    use HasFactory;

    protected $table = 'permintaan_headers';

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
        'completed_at',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'tanggal_dibutuhkan' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PermintaanItem::class, 'permintaan_header_id');
    }

    public function activities()
    {
        return $this->hasMany(PermintaanActivity::class, 'permintaan_header_id');
    }

    // Helper methods untuk status
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isReview()
    {
        return $this->status === 'review';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Update counters
    public function updateCounters()
    {
        $this->total_items = $this->items()->count();
        $this->approved_items = $this->items()->where('status', 'approved')->count();
        $this->rejected_items = $this->items()->where('status', 'rejected')->count();
        $this->save();
        
        // Auto update status berdasarkan item status
        if ($this->total_items > 0) {
            if ($this->rejected_items === $this->total_items) {
                $this->status = 'rejected';
            } elseif ($this->approved_items === $this->total_items) {
                $this->status = 'approved';
            } elseif ($this->approved_items > 0 || $this->rejected_items > 0) {
                $this->status = 'partial';
            }
            $this->save();
        }
    }

    // Get formatted tanggal for display (dd/mm/yyyy)
    public function getTanggalPermintaanFormattedAttribute()
    {
        return $this->tanggal_permintaan ? $this->tanggal_permintaan->format('d/m/Y') : '-';
    }

    public function getTanggalDibutuhkanFormattedAttribute()
    {
        return $this->tanggal_dibutuhkan ? $this->tanggal_dibutuhkan->format('d/m/Y') : '-';
    }
    
    // Get formatted tanggal for input field (dd/mm/yyyy) - untuk edit form
    public function getTanggalPermintaanInputAttribute()
    {
        return $this->tanggal_permintaan ? $this->tanggal_permintaan->format('d/m/Y') : '';
    }

    public function getTanggalDibutuhkanInputAttribute()
    {
        return $this->tanggal_dibutuhkan ? $this->tanggal_dibutuhkan->format('d/m/Y') : '';
    }
    
    // Method untuk cek apakah bisa diedit (digunakan oleh PermintaanItemController)
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    // Boot method untuk auto generate nomor
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($permintaan) {
            if (empty($permintaan->nomor_permintaan)) {
                $permintaan->nomor_permintaan = 'REQ-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1, 
                    4, '0', STR_PAD_LEFT
                );
            }
        });
    }
}