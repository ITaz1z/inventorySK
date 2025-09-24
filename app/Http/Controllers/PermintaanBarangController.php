<?php
// File: app/Http/Controllers/PermintaanBarangController.php
// Replace seluruh content file ini dengan kode dibawah

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanBarangController extends Controller
{
    // Tampilkan daftar permintaan (untuk admin gudang)
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdminGudang()) {
            // Admin gudang hanya lihat permintaannya sendiri
            $permintaans = PermintaanBarang::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user->isPurchasing()) {
            // Purchasing lihat semua permintaan yang pending
            $permintaans = PermintaanBarang::where('status', 'pending')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Manager lihat semua
            $permintaans = PermintaanBarang::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('permintaan.index', compact('permintaans'));
    }

    // Form buat permintaan baru (hanya admin gudang)
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }

        return view('permintaan.create');
    }

    // Simpan permintaan baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'tanggal_butuh' => 'nullable|date|after:today'
        ]);

        // Tentukan kategori berdasarkan role user
        $kategori = $user->role === 'admin_gudang_sparepart' ? 'sparepart' : 'umum';

        PermintaanBarang::create([
            'user_id' => $user->id,
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $kategori,
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'keterangan' => $validated['keterangan'],
            'tanggal_butuh' => $validated['tanggal_butuh'],
            'status' => 'pending'
        ]);

        return redirect()->route('permintaan.index')
            ->with('success', 'Permintaan barang berhasil dibuat!');
    }

    // Detail permintaan
    public function show(PermintaanBarang $permintaan)
    {
        $user = Auth::user();
        
        // Cek akses - admin gudang hanya bisa lihat miliknya, purchasing & manager bisa lihat semua
        if ($user->isAdminGudang() && $permintaan->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('permintaan.show', compact('permintaan'));
    }

    // Form edit (hanya admin gudang untuk permintaan pending)
    public function edit(PermintaanBarang $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id || $permintaan->status !== 'pending') {
            abort(403, 'Unauthorized');
        }

        return view('permintaan.edit', compact('permintaan'));
    }

    // Update permintaan
    public function update(Request $request, PermintaanBarang $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id || $permintaan->status !== 'pending') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'tanggal_butuh' => 'nullable|date|after:today'
        ]);

        $permintaan->update($validated);

        return redirect()->route('permintaan.index')
            ->with('success', 'Permintaan barang berhasil diupdate!');
    }

    // Hapus permintaan (hanya admin gudang untuk permintaan pending)
    public function destroy(PermintaanBarang $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id || $permintaan->status !== 'pending') {
            abort(403, 'Unauthorized');
        }

        $permintaan->delete();

        return redirect()->route('permintaan.index')
            ->with('success', 'Permintaan barang berhasil dihapus!');
    }

    // Method untuk purchasing review permintaan
    public function review(Request $request, PermintaanBarang $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:review,approved,rejected',
            'catatan_review' => 'nullable|string'
        ]);

        $permintaan->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()
            ->with('success', 'Status permintaan berhasil diupdate!');
    }
}