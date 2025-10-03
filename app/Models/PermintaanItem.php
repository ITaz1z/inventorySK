<?php
// File: app/Models/PermintaanItem.php (Updated)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_header_id',
        'master_barang_id',
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
        'is_urgent',
        'stok_tersedia_saat_request',
        'ada_stok_mencukupi',
        'tipe_permintaan'
    ];

    protected $casts = [
        'gambar_metadata' => 'array',
        'reviewed_at' => 'datetime',
        'jumlah' => 'integer',
        'jumlah_disetujui' => 'integer',
        'urutan' => 'integer',
        'is_urgent' => 'boolean',
        'stok_tersedia_saat_request' => 'integer',
        'ada_stok_mencukupi' => 'boolean'
    ];

    // Relationships
    public function header()
    {
        return $this->belongsTo(PermintaanHeader::class, 'permintaan_header_id');
    }

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeDariStok($query)
    {
        return $query->where('tipe_permintaan', 'dari_stok');
    }

    public function scopeRequestBaru($query)
    {
        return $query->where('tipe_permintaan', 'request_baru');
    }

    public function scopeStokMencukupi($query)
    {
        return $query->where('ada_stok_mencukupi', true);
    }

    public function scopeStokTidakMencukupi($query)
    {
        return $query->where('ada_stok_mencukupi', false);
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

    public function getTipePermintaanLabel()
    {
        return [
            'dari_stok' => 'Dari Stok',
            'request_baru' => 'Request Baru'
        ][$this->tipe_permintaan] ?? $this->tipe_permintaan;
    }

    public function getTipePermintaanColor()
    {
        return [
            'dari_stok' => 'success',
            'request_baru' => 'primary'
        ][$this->tipe_permintaan] ?? 'secondary';
    }

    public function getStokStatusLabel()
    {
        if ($this->tipe_permintaan === 'request_baru') {
            return 'Request Baru';
        }

        if ($this->ada_stok_mencukupi) {
            return "Stok: {$this->stok_tersedia_saat_request}";
        } else {
            return "Stok Kurang: {$this->stok_tersedia_saat_request}";
        }
    }

    public function getStokStatusColor()
    {
        if ($this->tipe_permintaan === 'request_baru') {
            return 'info';
        }

        return $this->ada_stok_mencukupi ? 'success' : 'warning';
    }

    // Method untuk cek stok terkini (real-time dari master_barang)
    public function getCurrentStokInfo()
    {
        if (!$this->masterBarang) {
            return [
                'stok_tersedia' => 0,
                'stok_aktual' => 0,
                'mencukupi' => false,
                'status' => 'Barang tidak ditemukan'
            ];
        }

        $stokAktual = $this->masterBarang->getStokTersediaAktual();
        
        return [
            'stok_tersedia' => $this->masterBarang->stok_tersedia,
            'stok_reserved' => $this->masterBarang->stok_reserved,
            'stok_aktual' => $stokAktual,
            'mencukupi' => $stokAktual >= $this->jumlah,
            'status' => $this->masterBarang->getStatusStokLabel()
        ];
    }

    // Method untuk reserve/unreserve stok
    public function reserveStok()
    {
        if ($this->masterBarang && $this->tipe_permintaan === 'dari_stok') {
            try {
                $this->masterBarang->reserveStok($this->jumlah);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function unreserveStok()
    {
        if ($this->masterBarang && $this->tipe_permintaan === 'dari_stok') {
            $this->masterBarang->unreserveStok($this->jumlah);
        }
    }

    // Event handlers
    protected static function boot()
    {
        parent::boot();

        // Ketika item dibuat
        static::creating(function ($item) {
            // Auto-set kategori dan cek stok jika ada master_barang_id
            if ($item->master_barang_id) {
                $masterBarang = MasterBarang::find($item->master_barang_id);
                if ($masterBarang) {
                    $item->kategori = $masterBarang->kategori;
                    $item->nama_barang = $masterBarang->nama_barang;
                    $item->satuan = $masterBarang->satuan;
                    
                    $stokAktual = $masterBarang->getStokTersediaAktual();
                    $item->stok_tersedia_saat_request = $stokAktual;
                    $item->ada_stok_mencukupi = $stokAktual >= $item->jumlah;
                    $item->tipe_permintaan = 'dari_stok';
                }
            } else {
                $item->tipe_permintaan = 'request_baru';
                $item->ada_stok_mencukupi = true; // Default untuk request baru
            }
        });

        // Ketika item di-approve dan ada stok
        static::updated(function ($item) {
            if ($item->isDirty('status') && 
                in_array($item->status, ['approved', 'partial']) &&
                $item->tipe_permintaan === 'dari_stok' &&
                $item->masterBarang) {
                
                // Reserve stok untuk approved items
                $approvedQty = $item->getApprovedQuantity();
                if ($approvedQty > 0) {
                    try {
                        $item->masterBarang->reserveStok($approvedQty);
                    } catch (\Exception $e) {
                        // Log error tapi jangan block update
                        \Log::warning("Failed to reserve stock for item {$item->id}: " . $e->getMessage());
                    }
                }
            }
        });

        // Ketika item dihapus, unreserve stok
        static::deleting(function ($item) {
            if ($item->tipe_permintaan === 'dari_stok' && $item->masterBarang) {
                $item->unreserveStok();
            }
        });
    }
}