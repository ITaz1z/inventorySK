<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_id',
        'purchasing_user_id',
        'nomor_po',
        'supplier',
        'total_harga',
        'tanggal_po',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_po' => 'date',
            'total_harga' => 'decimal:2',
        ];
    }

    // Relationship: PO dari permintaan barang
    public function permintaanBarang()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id');
    }

    // Relationship: PO dibuat oleh user purchasing
    public function purchasingUser()
    {
        return $this->belongsTo(User::class, 'purchasing_user_id');
    }

    // Auto generate nomor PO
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($po) {
            if (empty($po->nomor_po)) {
                $po->nomor_po = 'PO-' . date('Ymd') . '-' . str_pad((static::whereDate('created_at', today())->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }
}