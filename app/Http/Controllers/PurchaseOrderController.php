<?php
// File: app/Http/Controllers/PurchaseOrderController.php
// Copy ini ke file app/Http/Controllers/PurchaseOrderController.php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    // Daftar PO
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isPurchasing()) {
            // Purchasing hanya lihat PO yang dibuat sendiri
            $purchaseOrders = PurchaseOrder::where('purchasing_user_id', $user->id)
                ->with(['permintaanBarang.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Manager lihat semua PO
            $purchaseOrders = PurchaseOrder::with(['permintaanBarang.user', 'purchasingUser'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    // Form buat PO baru (dari permintaan)
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing()) {
            abort(403, 'Unauthorized');
        }

        $permintaan_id = $request->get('permintaan_id');
        $permintaan = null;

        if ($permintaan_id) {
            $permintaan = PermintaanBarang::where('id', $permintaan_id)
                ->where('status', 'approved')
                ->whereDoesntHave('purchaseOrder')
                ->first();
        }

        return view('purchase-orders.create', compact('permintaan'));
    }

    // Simpan PO baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'permintaan_id' => 'required|exists:permintaan_barangs,id',
            'supplier' => 'required|string|max:200',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_po' => 'required|date',
            'catatan' => 'nullable|string'
        ]);

        // Cek apakah permintaan sudah punya PO
        $permintaan = PermintaanBarang::findOrFail($validated['permintaan_id']);
        
        if ($permintaan->purchaseOrder) {
            return redirect()->back()
                ->withErrors(['permintaan_id' => 'Permintaan ini sudah memiliki PO']);
        }

        PurchaseOrder::create([
            'permintaan_id' => $validated['permintaan_id'],
            'purchasing_user_id' => $user->id,
            'supplier' => $validated['supplier'],
            'total_harga' => $validated['total_harga'],
            'tanggal_po' => $validated['tanggal_po'],
            'catatan' => $validated['catatan'],
            'status' => 'draft'
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dibuat!');
    }

    // Detail PO
    public function show(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        
        // Cek akses - purchasing hanya bisa lihat miliknya
        if ($user->isPurchasing() && $purchaseOrder->purchasing_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $purchaseOrder->load(['permintaanBarang.user', 'purchasingUser']);
        
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    // Form edit PO (hanya draft)
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing() || 
            $purchaseOrder->purchasing_user_id !== $user->id || 
            $purchaseOrder->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        return view('purchase-orders.edit', compact('purchaseOrder'));
    }

    // Update PO
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing() || 
            $purchaseOrder->purchasing_user_id !== $user->id || 
            $purchaseOrder->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'supplier' => 'required|string|max:200',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_po' => 'required|date',
            'catatan' => 'nullable|string'
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil diupdate!');
    }

    // Hapus PO (hanya draft)
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing() || 
            $purchaseOrder->purchasing_user_id !== $user->id || 
            $purchaseOrder->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus!');
    }

    // Method untuk kirim PO
    public function send(PurchaseOrder $purchaseOrder)
    {
        $user = Auth::user();
        
        if (!$user->isPurchasing() || 
            $purchaseOrder->purchasing_user_id !== $user->id || 
            $purchaseOrder->status !== 'draft') {
            abort(403, 'Unauthorized');
        }

        $purchaseOrder->update(['status' => 'sent']);

        return redirect()->back()
            ->with('success', 'Purchase Order berhasil dikirim!');
    }
}