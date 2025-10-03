<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'deskripsi',
        'stok_minimum',
        'stok_maksimum',
        'stok_tersedia',
        'stok_reserved',
        'lokasi_gudang',
        'harga_rata_rata',
        'supplier_utama',
        'is_active',
        'status_stok',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'stok_minimum' => 'integer',
        'stok_maksimum' => 'integer',
        'stok_tersedia' => 'integer',
        'stok_reserved' => 'integer',
        'harga_rata_rata' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function permintaanItems()
    {
        return $this->hasMany(PermintaanItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeStokRendah($query)
    {
        return $query->whereRaw('stok_tersedia <= stok_minimum');
    }

    public function scopeStokHabis($query)
    {
        return $query->where('stok_tersedia', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_barang', 'ILIKE', "%{$search}%")
              ->orWhere('kode_barang', 'ILIKE', "%{$search}%")
              ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
        });
    }

    // Helper Methods
    public function getStokTersediaAktual()
    {
        return $this->stok_tersedia - $this->stok_reserved;
    }

    public function isStokMencukupi($jumlah)
    {
        return $this->getStokTersediaAktual() >= $jumlah;
    }

    public function isStokMinimum()
    {
        return $this->stok_tersedia <= $this->stok_minimum;
    }

    public function isStokHabis()
    {
        return $this->stok_tersedia <= 0;
    }

    public function getStatusStokLabel()
    {
        return [
            'normal' => 'Normal',
            'minimum' => 'Stok Minimum',
            'habis' => 'Stok Habis',
            'over_stock' => 'Over Stock'
        ][$this->status_stok] ?? $this->status_stok;
    }

    public function getStatusStokColor()
    {
        return [
            'normal' => 'success',
            'minimum' => 'warning',
            'habis' => 'danger',
            'over_stock' => 'info'
        ][$this->status_stok] ?? 'secondary';
    }

    // Reserve dan unreserve stok
    public function reserveStok($jumlah)
    {
        if (!$this->isStokMencukupi($jumlah)) {
            throw new \Exception("Stok tidak mencukupi. Tersedia: {$this->getStokTersediaAktual()}, Diminta: {$jumlah}");
        }

        $this->increment('stok_reserved', $jumlah);
        $this->updateStatusStok();
    }

    public function unreserveStok($jumlah)
    {
        $this->decrement('stok_reserved', min($jumlah, $this->stok_reserved));
        $this->updateStatusStok();
    }

    public function kurangiStok($jumlah)
    {
        if ($this->stok_tersedia < $jumlah) {
            throw new \Exception("Stok tidak mencukupi untuk dikurangi");
        }

        $this->decrement('stok_tersedia', $jumlah);
        $this->updateStatusStok();
    }

    public function tambahStok($jumlah)
    {
        $this->increment('stok_tersedia', $jumlah);
        $this->updateStatusStok();
    }

    // Auto update status stok
    public function updateStatusStok()
    {
        $stokAktual = $this->getStokTersediaAktual();
        
        if ($stokAktual <= 0) {
            $status = 'habis';
        } elseif ($stokAktual <= $this->stok_minimum) {
            $status = 'minimum';
        } elseif ($this->stok_maksimum && $stokAktual >= $this->stok_maksimum) {
            $status = 'over_stock';
        } else {
            $status = 'normal';
        }

        $this->update(['status_stok' => $status]);
    }

    // Auto generate kode barang
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($barang) {
            if (empty($barang->kode_barang)) {
                $prefix = $barang->kategori === 'sparepart' ? 'SP' : 'UM';
                $count = static::where('kategori', $barang->kategori)->count() + 1;
                $barang->kode_barang = $prefix . date('y') . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($barang) {
            if (auth()->check()) {
                $barang->updated_by = auth()->id();
            }
        });
    }
}