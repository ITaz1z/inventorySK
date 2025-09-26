<?php
// File: app/Http/Controllers/PermintaanItemController.php
// Controller untuk menangani CRUD items dalam permintaan

namespace App\Http\Controllers;

use App\Models\PermintaanHeader;
use App\Models\PermintaanItem;
use App\Models\PermintaanActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PermintaanItemController extends Controller
{
    // Tambah item ke permintaan
    public function store(Request $request, PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->canBeEdited()) {
            abort(403, 'Tidak dapat menambah item');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
            'is_urgent' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();
        
        try {
            // Handle upload gambar
            $gambarPath = null;
            $gambarMetadata = null;
            
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $gambarPath = $file->store('permintaan-items', 'public');
                $gambarMetadata = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }

            // Tentukan kategori berdasarkan role user
            $kategori = $user->role === 'admin_gudang_sparepart' ? 'sparepart' : 'umum';

            // Tentukan urutan berikutnya
            $nextOrder = $permintaan->items()->max('urutan') + 1;

            $item = PermintaanItem::create([
                'permintaan_header_id' => $permintaan->id,
                'nama_barang' => $validated['nama_barang'],
                'kategori' => $kategori,
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'keterangan' => $validated['keterangan'],
                'gambar_path' => $gambarPath,
                'gambar_metadata' => $gambarMetadata,
                'is_urgent' => $validated['is_urgent'] ?? false,
                'urutan' => $nextOrder,
                'status' => 'pending'
            ]);

            // Update counter di header
            $permintaan->update([
                'total_items' => $permintaan->items()->count()
            ]);

            // Log activity
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'item_added',
                "Item '{$item->nama_barang}' ditambahkan",
                ['item_id' => $item->id]
            );

            DB::commit();

            return back()->with('success', 'Item berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Hapus file jika ada error
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }
            
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Update item
    public function update(Request $request, PermintaanHeader $permintaan, PermintaanItem $item)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            $item->permintaan_header_id !== $permintaan->id ||
            !$permintaan->canBeEdited()) {
            abort(403, 'Tidak dapat mengubah item');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
            'is_urgent' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hapus_gambar' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            $oldGambarPath = $item->gambar_path;
            $gambarPath = $oldGambarPath;
            $gambarMetadata = $item->gambar_metadata;

            // Handle hapus gambar
            if ($validated['hapus_gambar'] ?? false) {
                if ($oldGambarPath) {
                    Storage::disk('public')->delete($oldGambarPath);
                }
                $gambarPath = null;
                $gambarMetadata = null;
            }

            // Handle upload gambar baru
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($oldGambarPath) {
                    Storage::disk('public')->delete($oldGambarPath);
                }
                
                $file = $request->file('gambar');
                $gambarPath = $file->store('permintaan-items', 'public');
                $gambarMetadata = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }

            $item->update([
                'nama_barang' => $validated['nama_barang'],
                'jumlah' => $validated['jumlah'],
                'satuan' => $validated['satuan'],
                'keterangan' => $validated['keterangan'],
                'is_urgent' => $validated['is_urgent'] ?? false,
                'gambar_path' => $gambarPath,
                'gambar_metadata' => $gambarMetadata
            ]);

            DB::commit();

            return back()->with('success', 'Item berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Hapus item
    public function destroy(PermintaanHeader $permintaan, PermintaanItem $item)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            $item->permintaan_header_id !== $permintaan->id ||
            !$permintaan->canBeEdited()) {
            abort(403, 'Tidak dapat menghapus item');
        }

        DB::beginTransaction();
        
        try {
            $itemName = $item->nama_barang;
            
            // Hapus gambar jika ada
            if ($item->gambar_path) {
                Storage::disk('public')->delete($item->gambar_path);
            }

            $item->delete();

            // Update counter di header
            $permintaan->update([
                'total_items' => $permintaan->items()->count()
            ]);

            // Log activity
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'item_removed',
                "Item '{$itemName}' dihapus"
            );

            DB::commit();

            return back()->with('success', 'Item berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Review item individual (untuk purchasing)
    public function review(Request $request, PermintaanHeader $permintaan, PermintaanItem $item)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing()) {
            abort(403, 'Hanya Purchasing yang bisa review item');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,partial',
            'catatan_review' => 'nullable|string',
            'jumlah_disetujui' => 'nullable|integer|min:0|max:' . $item->jumlah
        ]);

        DB::beginTransaction();
        
        try {
            // Untuk status partial, jumlah_disetujui wajib
            if ($validated['status'] === 'partial' && empty($validated['jumlah_disetujui'])) {
                return back()->withErrors(['jumlah_disetujui' => 'Jumlah disetujui wajib untuk status partial']);
            }

            $item->update([
                'status' => $validated['status'],
                'catatan_review' => $validated['catatan_review'],
                'jumlah_disetujui' => $validated['status'] === 'partial' ? $validated['jumlah_disetujui'] : null,
                'reviewed_by' => $user->id,
                'reviewed_at' => now()
            ]);

            // Update counters di header
            $approvedCount = $permintaan->items()->where('status', 'approved')->count();
            $partialCount = $permintaan->items()->where('status', 'partial')->count();
            $rejectedCount = $permintaan->items()->where('status', 'rejected')->count();

            $newStatus = 'review';
            if ($approvedCount + $partialCount + $rejectedCount === $permintaan->total_items) {
                // Semua item sudah direview
                if ($rejectedCount === $permintaan->total_items) {
                    $newStatus = 'rejected';
                } elseif ($approvedCount === $permintaan->total_items) {
                    $newStatus = 'approved';
                } else {
                    $newStatus = 'partial';
                }
            }

            $permintaan->update([
                'status' => $newStatus,
                'approved_items' => $approvedCount + $partialCount,
                'rejected_items' => $rejectedCount,
                'reviewed_at' => now()
            ]);

            // Log activity
            $statusLabel = $item->getStatusLabel();
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'item_' . $validated['status'],
                "Item '{$item->nama_barang}' {$statusLabel} oleh {$user->name}",
                ['item_id' => $item->id]
            );

            DB::commit();

            return back()->with('success', "Item berhasil di-{$statusLabel}");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Update urutan item (drag & drop)
    public function updateOrder(Request $request, PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->canBeEdited()) {
            abort(403, 'Tidak dapat mengubah urutan');
        }

        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:permintaan_items,id'
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($validated['item_ids'] as $index => $itemId) {
                PermintaanItem::where('id', $itemId)
                    ->where('permintaan_header_id', $permintaan->id)
                    ->update(['urutan' => $index + 1]);
            }

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}