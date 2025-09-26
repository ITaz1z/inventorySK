<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_header_id',
        'nama_barang',
        'kategori',
        'jumlah',
        'satuan',
        'keterangan',
        'gambar_path',
        'gambar_metadata',
        'status',
        'catatan_review',
        'reviewed_by',
        'reviewed_at',
        'jumlah_disetujui',
        'urutan',
        'is_urgent'
    ];

    protected $casts = [
        'gambar_metadata' => 'array',
        'reviewed_at' => 'datetime',
        'jumlah' => 'integer',
        'jumlah_disetujui' => 'integer',
        'urutan' => 'integer',
        'is_urgent' => 'boolean'
    ];

    // Relationships
    public function header()
    {
        return $this->belongsTo(PermintaanHeader::class, 'permintaan_header_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Helper Methods
    public function getStatusLabel()
    {
        return [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'partial' => 'Sebagian'
        ][$this->status] ?? $this->status;
    }

    public function getApprovedQuantity()
    {
        if ($this->status === 'approved') {
            return $this->jumlah;
        }
        
        if ($this->status === 'partial') {
            return $this->jumlah_disetujui ?? 0;
        }
        
        return 0;
    }
}