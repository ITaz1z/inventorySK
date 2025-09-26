<?php
// File: app/Models/PermintaanActivity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_header_id',
        'user_id',
        'activity_type',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function permintaanHeader()
    {
        return $this->belongsTo(PermintaanHeader::class, 'permintaan_header_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static method untuk logging activity
    public static function log($permintaanHeaderId, $userId, $activityType, $description, $metadata = null)
    {
        return static::create([
            'permintaan_header_id' => $permintaanHeaderId,
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    // Helper method untuk mendapatkan icon berdasarkan activity type
    public function getActivityIcon()
    {
        return [
            'created' => 'fas fa-plus-circle text-success',
            'submitted' => 'fas fa-paper-plane text-primary',
            'item_added' => 'fas fa-plus text-success',
            'item_removed' => 'fas fa-trash text-danger',
            'item_approved' => 'fas fa-check-circle text-success',
            'item_rejected' => 'fas fa-times-circle text-danger',
            'status_changed' => 'fas fa-exchange-alt text-info',
            'po_created' => 'fas fa-file-invoice text-success',
            'comment_added' => 'fas fa-comment text-info',
            'priority_changed' => 'fas fa-flag text-warning',
        ][$this->activity_type] ?? 'fas fa-info-circle text-muted';
    }

    // Helper method untuk mendapatkan label activity type
    public function getActivityLabel()
    {
        return [
            'created' => 'Dibuat',
            'submitted' => 'Disubmit',
            'item_added' => 'Item Ditambah',
            'item_removed' => 'Item Dihapus',
            'item_approved' => 'Item Disetujui',
            'item_rejected' => 'Item Ditolak',
            'status_changed' => 'Status Berubah',
            'po_created' => 'PO Dibuat',
            'comment_added' => 'Komentar Ditambah',
            'priority_changed' => 'Prioritas Berubah',
        ][$this->activity_type] ?? ucfirst(str_replace('_', ' ', $this->activity_type));
    }

    // Scope untuk mendapatkan aktivitas terbaru
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    // Scope untuk mendapatkan aktivitas berdasarkan type
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }
}