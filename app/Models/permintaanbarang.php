<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_barang',
        'kategori',
        'jumlah',
        'satuan',
        'keterangan',
        'status',
        'tanggal_butuh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_butuh' => 'datetime',
        ];
    }

    // Relationship: Permintaan barang milik user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Permintaan barang punya PO
    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'permintaan_id');
    }

    // Helper method untuk status
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
}

