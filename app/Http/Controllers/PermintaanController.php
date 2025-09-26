<?php
// File: app/Http/Controllers/PermintaanController.php
// Controller baru untuk menangani struktur permintaan header-item

namespace App\Http\Controllers;

use App\Models\PermintaanHeader;
use App\Models\PermintaanItem;
use App\Models\PermintaanActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PermintaanController extends Controller
{
    // Daftar semua permintaan
    public function index()
    {
        $user = Auth::user();
        
        $query = PermintaanHeader::with(['user', 'items'])
            ->forUser($user)
            ->latest();

        // Filter berdasarkan parameter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('priority')) {
            $query->where('tingkat_prioritas', request('priority'));
        }

        $permintaans = $query->paginate(10);

        return view('permintaan.index', compact('permintaans'));
    }

    // Form create permintaan baru
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Hanya Admin Gudang yang bisa membuat permintaan');
        }

        return view('permintaan.create');
    }

    // Simpan permintaan baru (header saja dulu)
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'judul_permintaan' => 'required|string|max:255',
            'tanggal_dibutuhkan' => 'required|date|after:today',
            'tingkat_prioritas' => 'required|in:urgent,penting,routine,non_routine',
            'catatan_permintaan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            $header = PermintaanHeader::create([
                'user_id' => $user->id,
                'judul_permintaan' => $validated['judul_permintaan'],
                'tanggal_permintaan' => now()->format('Y-m-d'),
                'tanggal_dibutuhkan' => $validated['tanggal_dibutuhkan'],
                'tingkat_prioritas' => $validated['tingkat_prioritas'],
                'catatan_permintaan' => $validated['catatan_permintaan'],
                'status' => 'draft'
            ]);

            // Log activity
            PermintaanActivity::log(
                $header->id, 
                $user->id, 
                'created', 
                "Permintaan '{$header->judul_permintaan}' dibuat"
            );

            DB::commit();

            return redirect()->route('permintaan.show', $header)
                ->with('success', 'Permintaan berhasil dibuat! Silahkan tambahkan item barang.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Detail permintaan dengan items
    public function show(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->isAdminGudang() && $permintaan->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $permintaan->load([
            'user', 
            'items' => function($query) {
                $query->orderBy('urutan');
            }, 
            'activities' => function($query) {
                $query->with('user')->latest();
            }
        ]);

        return view('permintaan.manage-items', compact('permintaan'));
    }

    // Form edit header permintaan
    public function edit(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->canBeEdited()) {
            abort(403, 'Permintaan tidak dapat diedit');
        }

        return view('permintaan.edit', compact('permintaan'));
    }

    // Update header permintaan
    public function update(Request $request, PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->canBeEdited()) {
            abort(403, 'Permintaan tidak dapat diedit');
        }

        $validated = $request->validate([
            'judul_permintaan' => 'required|string|max:255',
            'tanggal_dibutuhkan' => 'required|date|after:today',
            'tingkat_prioritas' => 'required|in:urgent,penting,routine,non_routine',
            'catatan_permintaan' => 'nullable|string'
        ]);

        $oldPriority = $permintaan->tingkat_prioritas;
        
        $permintaan->update($validated);

        // Log jika priority berubah
        if ($oldPriority !== $permintaan->tingkat_prioritas) {
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'priority_changed',
                "Prioritas diubah dari {$oldPriority} ke {$permintaan->tingkat_prioritas}"
            );
        }

        return redirect()->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan berhasil diupdate');
    }

    // Submit permintaan (status dari draft ke pending)
    public function submit(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->isDraft()) {
            abort(403, 'Permintaan tidak dapat disubmit');
        }

        // Pastikan ada minimal 1 item
        if ($permintaan->items()->count() === 0) {
            return back()->withErrors(['error' => 'Tambahkan minimal 1 item sebelum submit']);
        }

        DB::beginTransaction();
        
        try {
            $permintaan->update([
                'status' => 'pending',
                'submitted_at' => now()
            ]);

            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'submitted',
                "Permintaan disubmit untuk review"
            );

            DB::commit();

            return redirect()->route('permintaan.show', $permintaan)
                ->with('success', 'Permintaan berhasil disubmit dan menunggu review');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Hapus permintaan (hanya draft dan milik sendiri)
    public function destroy(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || 
            $permintaan->user_id !== $user->id || 
            !$permintaan->isDraft()) {
            abort(403, 'Permintaan tidak dapat dihapus');
        }

        DB::beginTransaction();
        
        try {
            // Hapus gambar jika ada
            foreach ($permintaan->items as $item) {
                if ($item->gambar_path) {
                    Storage::disk('public')->delete($item->gambar_path);
                }
            }

            $judul = $permintaan->judul_permintaan;
            $permintaan->delete();

            DB::commit();

            return redirect()->route('permintaan.index')
                ->with('success', "Permintaan '{$judul}' berhasil dihapus");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Method khusus untuk purchasing review
    public function review(Request $request, PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing()) {
            abort(403, 'Hanya Purchasing yang bisa review');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve_all,reject_all',
            'catatan_review' => 'nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            if ($validated['action'] === 'approve_all') {
                $permintaan->items()->update([
                    'status' => 'approved',
                    'catatan_review' => $validated['catatan_review'],
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now()
                ]);

                $permintaan->update([
                    'status' => 'approved',
                    'reviewed_at' => now(),
                    'approved_items' => $permintaan->total_items
                ]);

                $message = 'Semua item disetujui';

            } else {
                $permintaan->items()->update([
                    'status' => 'rejected',
                    'catatan_review' => $validated['catatan_review'],
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now()
                ]);

                $permintaan->update([
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'rejected_items' => $permintaan->total_items
                ]);

                $message = 'Semua item ditolak';
            }

            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'status_changed',
                $message . ' oleh ' . $user->name
            );

            DB::commit();

            return back()->with('success', 'Review berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}