<?php
// File: app/Http/Controllers/PermintaanController.php
// Controller untuk CRUD Permintaan Header dengan format tanggal dd/mm/yyyy

namespace App\Http\Controllers;

use App\Models\PermintaanHeader;
use App\Models\PermintaanActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanController extends Controller
{
    /**
     * Convert date from dd/mm/yyyy to yyyy-mm-dd untuk database
     */
    private function convertDateToDatabase($date)
    {
        if (empty($date)) {
            return null;
        }
        
        // Cek apakah format dd/mm/yyyy
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            // dd/mm/yyyy -> yyyy-mm-dd
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        // Jika sudah format yyyy-mm-dd, return as is
        return $date;
    }
    
    /**
     * Tampilkan daftar permintaan
     */
    public function index()
    {
        $user = Auth::user();
        
        // Filter berdasarkan request
        $query = PermintaanHeader::query();
        
        if ($user->isAdminGudang()) {
            // Admin gudang hanya lihat miliknya
            $query->where('user_id', $user->id);
        } elseif ($user->isPurchasing()) {
            // Purchasing lihat yang perlu direview
            $query->whereIn('status', ['pending', 'review']);
        }
        // Manager lihat semua (tidak ada filter tambahan)
        
        // Apply filters dari request
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('priority')) {
            $query->where('tingkat_prioritas', request('priority'));
        }
        
        $permintaans = $query->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('permintaan.index', compact('permintaans'));
    }

    /**
     * Form buat permintaan baru (hanya admin gudang)
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Hanya Admin Gudang yang dapat membuat permintaan');
        }

        return view('permintaan.create');
    }

    /**
     * Simpan permintaan baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang()) {
            abort(403, 'Hanya Admin Gudang yang dapat membuat permintaan');
        }

        $validated = $request->validate([
            'judul_permintaan' => 'required|string|max:255',
            'tanggal_permintaan' => 'required|string', // Format dd/mm/yyyy
            'tanggal_dibutuhkan' => 'required|string', // Format dd/mm/yyyy
            'tingkat_prioritas' => 'required|in:urgent,penting,routine,non_routine',
            'catatan_permintaan' => 'nullable|string'
        ], [
            'judul_permintaan.required' => 'Judul permintaan harus diisi',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi',
            'tanggal_dibutuhkan.required' => 'Tanggal dibutuhkan harus diisi',
            'tingkat_prioritas.required' => 'Tingkat prioritas harus dipilih',
        ]);

        // Convert tanggal dari dd/mm/yyyy ke yyyy-mm-dd
        $tanggalPermintaan = $this->convertDateToDatabase($validated['tanggal_permintaan']);
        $tanggalDibutuhkan = $this->convertDateToDatabase($validated['tanggal_dibutuhkan']);
        
        // Validasi tanggal dibutuhkan harus lebih dari hari ini
        if (Carbon::parse($tanggalDibutuhkan)->lte(Carbon::today())) {
            return back()->withErrors([
                'tanggal_dibutuhkan' => 'Tanggal dibutuhkan harus minimal besok (H+1)'
            ])->withInput();
        }
        
        // Validasi tanggal permintaan tidak boleh lebih dari tanggal dibutuhkan
        if (Carbon::parse($tanggalPermintaan)->gt(Carbon::parse($tanggalDibutuhkan))) {
            return back()->withErrors([
                'tanggal_permintaan' => 'Tanggal permintaan tidak boleh lebih dari tanggal dibutuhkan'
            ])->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Generate nomor permintaan
            $nomorPermintaan = 'REQ-' . date('Ymd') . '-' . str_pad(
                PermintaanHeader::whereDate('created_at', today())->count() + 1, 
                4, '0', STR_PAD_LEFT
            );

            $permintaan = PermintaanHeader::create([
                'user_id' => $user->id,
                'nomor_permintaan' => $nomorPermintaan,
                'judul_permintaan' => $validated['judul_permintaan'],
                'tanggal_permintaan' => $tanggalPermintaan,
                'tanggal_dibutuhkan' => $tanggalDibutuhkan,
                'tingkat_prioritas' => $validated['tingkat_prioritas'],
                'catatan_permintaan' => $validated['catatan_permintaan'],
                'status' => 'draft',
                'total_items' => 0,
                'approved_items' => 0,
                'rejected_items' => 0
            ]);

            // Log activity
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'created',
                "Permintaan '{$permintaan->judul_permintaan}' dibuat",
                ['nomor_permintaan' => $permintaan->nomor_permintaan]
            );

            DB::commit();

            return redirect()->route('permintaan.show', $permintaan)
                ->with('success', 'Permintaan berhasil dibuat! Silakan tambahkan item barang.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Detail permintaan
     */
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

    // UBAH INI: Gunakan view manage-items yang sudah ada
    return view('permintaan.manage-items', compact('permintaan'));
}

    /**
     * Form edit (hanya admin gudang untuk permintaan draft)
     */
    public function edit(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk edit permintaan ini');
        }
        
        if (!$permintaan->canBeEdited()) {
            return redirect()->route('permintaan.show', $permintaan)
                ->withErrors(['error' => 'Hanya permintaan dengan status draft/pending yang bisa diedit']);
        }

        return view('permintaan.edit', compact('permintaan'));
    }

    /**
     * Update permintaan
     */
    public function update(Request $request, PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk update permintaan ini');
        }
        
        if (!$permintaan->canBeEdited()) {
            return back()->withErrors(['error' => 'Permintaan ini tidak dapat diedit']);
        }

        $validated = $request->validate([
            'judul_permintaan' => 'required|string|max:255',
            'tanggal_permintaan' => 'required|string',
            'tanggal_dibutuhkan' => 'required|string',
            'tingkat_prioritas' => 'required|in:urgent,penting,routine,non_routine',
            'catatan_permintaan' => 'nullable|string'
        ], [
            'judul_permintaan.required' => 'Judul permintaan harus diisi',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi',
            'tanggal_dibutuhkan.required' => 'Tanggal dibutuhkan harus diisi',
            'tingkat_prioritas.required' => 'Tingkat prioritas harus dipilih',
        ]);

        // Convert tanggal
        $tanggalPermintaan = $this->convertDateToDatabase($validated['tanggal_permintaan']);
        $tanggalDibutuhkan = $this->convertDateToDatabase($validated['tanggal_dibutuhkan']);
        
        // Validasi tanggal
        if (Carbon::parse($tanggalDibutuhkan)->lte(Carbon::today())) {
            return back()->withErrors([
                'tanggal_dibutuhkan' => 'Tanggal dibutuhkan harus minimal besok (H+1)'
            ])->withInput();
        }
        
        if (Carbon::parse($tanggalPermintaan)->gt(Carbon::parse($tanggalDibutuhkan))) {
            return back()->withErrors([
                'tanggal_permintaan' => 'Tanggal permintaan tidak boleh lebih dari tanggal dibutuhkan'
            ])->withInput();
        }

        DB::beginTransaction();
        
        try {
            $permintaan->update([
                'judul_permintaan' => $validated['judul_permintaan'],
                'tanggal_permintaan' => $tanggalPermintaan,
                'tanggal_dibutuhkan' => $tanggalDibutuhkan,
                'tingkat_prioritas' => $validated['tingkat_prioritas'],
                'catatan_permintaan' => $validated['catatan_permintaan'],
            ]);

            // Log activity
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'status_changed',
                "Permintaan '{$permintaan->judul_permintaan}' diupdate"
            );

            DB::commit();

            return redirect()->route('permintaan.show', $permintaan)
                ->with('success', 'Permintaan berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Hapus permintaan (hanya admin gudang untuk permintaan draft)
     */
    public function destroy(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk hapus permintaan ini');
        }
        
        if ($permintaan->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya permintaan draft yang bisa dihapus']);
        }

        DB::beginTransaction();
        
        try {
            $judulPermintaan = $permintaan->judul_permintaan;
            
            // Hapus semua items (cascade akan handle ini jika ada foreign key)
            $permintaan->items()->delete();
            
            // Hapus permintaan
            $permintaan->delete();

            DB::commit();

            return redirect()->route('permintaan.index')
                ->with('success', "Permintaan '{$judulPermintaan}' berhasil dihapus");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Submit permintaan (ubah status dari draft ke pending)
     */
    public function submit(PermintaanHeader $permintaan)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGudang() || $permintaan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses');
        }
        
        if ($permintaan->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya permintaan draft yang bisa disubmit']);
        }
        
        // Cek apakah ada item
        if ($permintaan->items()->count() == 0) {
            return back()->withErrors([
                'error' => 'Tidak bisa submit permintaan tanpa item barang'
            ]);
        }

        DB::beginTransaction();
        
        try {
            $permintaan->update([
                'status' => 'pending',
                'submitted_at' => now()
            ]);

            // Log activity
            PermintaanActivity::log(
                $permintaan->id,
                $user->id,
                'submitted',
                "Permintaan '{$permintaan->judul_permintaan}' disubmit untuk review",
                ['total_items' => $permintaan->total_items]
            );

            DB::commit();

            return redirect()->route('permintaan.index')
                ->with('success', 'Permintaan berhasil disubmit untuk review!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}