<?php
// File: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\PermintaanHeader;
use App\Models\PermintaanItem;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Data untuk dashboard berdasarkan role
        $data = [
            'user' => $user,
        ];

        switch ($user->role) {
            case 'admin_gudang_umum':
            case 'admin_gudang_sparepart':
                // Data untuk Admin Gudang
                $data['total_permintaan'] = PermintaanHeader::where('user_id', $user->id)->count();
                $data['draft_permintaan'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'draft')->count();
                $data['pending_permintaan'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'pending')->count();
                $data['approved_permintaan'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'approved')->count();
                
                // Hitung total items dari semua permintaan user ini
                $data['total_items'] = PermintaanItem::whereHas('header', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count();
                
                // Recent permintaan
                $data['recent_permintaan'] = PermintaanHeader::where('user_id', $user->id)
                    ->with('items')
                    ->latest()
                    ->limit(5)
                    ->get();
                break;

            case 'purchasing_1':
            case 'purchasing_2':
                // Data untuk Purchasing
                $data['pending_review'] = PermintaanHeader::where('status', 'pending')->count();
                $data['in_review'] = PermintaanHeader::where('status', 'review')->count();
                $data['total_po_dibuat'] = PurchaseOrder::where('purchasing_user_id', $user->id)->count();
                $data['po_draft'] = PurchaseOrder::where('purchasing_user_id', $user->id)
                    ->where('status', 'draft')->count();
                
                // Hitung item urgent yang pending
                $data['urgent_items'] = PermintaanItem::whereHas('header', function($q) {
                    $q->where('tingkat_prioritas', 'urgent')
                      ->where('status', 'pending');
                })->where('status', 'pending')->count();
                
                // Permintaan yang butuh review
                $data['need_review'] = PermintaanHeader::where('status', 'pending')
                    ->with(['user', 'items'])
                    ->orderByRaw("FIELD(tingkat_prioritas, 'urgent', 'penting', 'routine', 'non_routine')")
                    ->orderBy('tanggal_dibutuhkan', 'asc')
                    ->limit(5)
                    ->get();
                break;

            case 'general_manager':
            case 'atasan':
                // Data untuk Manager
                $data['total_permintaan'] = PermintaanHeader::count();
                $data['total_po'] = PurchaseOrder::count();
                $data['pending_permintaan'] = PermintaanHeader::where('status', 'pending')->count();
                $data['urgent_permintaan'] = PermintaanHeader::where('tingkat_prioritas', 'urgent')->count();
                
                // Approved this month - hanya yang punya reviewed_at
                $data['approved_this_month'] = PermintaanHeader::where('status', 'approved')
                    ->whereNotNull('reviewed_at')
                    ->whereMonth('reviewed_at', now()->month)
                    ->whereYear('reviewed_at', now()->year)
                    ->count();
                
                // Summary by kategori
                $data['summary_kategori'] = PermintaanItem::selectRaw('
                        kategori, 
                        COUNT(*) as total, 
                        SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending
                    ')
                    ->groupBy('kategori')
                    ->get();
                break;
                
            default:
                // Fallback untuk role yang tidak dikenali
                $data['total_permintaan'] = 0;
                $data['pending_permintaan'] = 0;
                $data['approved_permintaan'] = 0;
                break;
        }

        return view('dashboard.index', $data);
    }

    /**
     * Method untuk mendapatkan statistik via AJAX
     */
    public function getStats()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->isAdminGudang()) {
            $stats = [
                'total_permintaan' => PermintaanHeader::where('user_id', $user->id)->count(),
                'draft' => PermintaanHeader::where('user_id', $user->id)->where('status', 'draft')->count(),
                'pending' => PermintaanHeader::where('user_id', $user->id)->where('status', 'pending')->count(),
                'approved' => PermintaanHeader::where('user_id', $user->id)->where('status', 'approved')->count(),
                'total_items' => PermintaanItem::whereHas('header', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count()
            ];
        } elseif ($user->isPurchasing()) {
            $stats = [
                'pending_review' => PermintaanHeader::where('status', 'pending')->count(),
                'in_review' => PermintaanHeader::where('status', 'review')->count(),
                'po_draft' => PurchaseOrder::where('purchasing_user_id', $user->id)->where('status', 'draft')->count(),
                'urgent_items' => PermintaanItem::whereHas('header', function($q) {
                    $q->where('tingkat_prioritas', 'urgent')->where('status', 'pending');
                })->where('status', 'pending')->count()
            ];
        } else {
            $stats = [
                'total_permintaan' => PermintaanHeader::count(),
                'total_po' => PurchaseOrder::count(),
                'urgent_pending' => PermintaanHeader::where('tingkat_prioritas', 'urgent')->where('status', 'pending')->count(),
                'approved_this_month' => PermintaanHeader::where('status', 'approved')
                    ->whereNotNull('reviewed_at')
                    ->whereMonth('reviewed_at', now()->month)
                    ->count()
            ];
        }

        return response()->json($stats);
    }

    /**
     * Method untuk mendapatkan recent activities
     */
    public function getRecentActivities()
    {
        $user = Auth::user();
        $activities = [];

        if ($user->isAdminGudang()) {
            // Recent permintaan untuk admin gudang
            $activities = PermintaanHeader::where('user_id', $user->id)
                ->with('items')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($permintaan) {
                    return [
                        'id' => $permintaan->id,
                        'judul' => $permintaan->judul_permintaan,
                        'status' => $permintaan->status,
                        'status_label' => $permintaan->getStatusLabel(),
                        'total_items' => $permintaan->items->count(),
                        'created_at' => $permintaan->created_at->diffForHumans()
                    ];
                });
        } elseif ($user->isPurchasing()) {
            // Permintaan yang butuh review untuk purchasing
            $activities = PermintaanHeader::where('status', 'pending')
                ->with(['user', 'items'])
                ->orderByRaw("FIELD(tingkat_prioritas, 'urgent', 'penting', 'routine', 'non_routine')")
                ->orderBy('tanggal_dibutuhkan', 'asc')
                ->limit(5)
                ->get()
                ->map(function($permintaan) {
                    return [
                        'id' => $permintaan->id,
                        'judul' => $permintaan->judul_permintaan,
                        'user' => $permintaan->user->name,
                        'prioritas' => $permintaan->tingkat_prioritas,
                        'prioritas_label' => $permintaan->getPriorityLabel(),
                        'tanggal_butuh' => $permintaan->tanggal_dibutuhkan->format('d M Y'),
                        'total_items' => $permintaan->items->count(),
                        'submitted_at' => $permintaan->submitted_at ? $permintaan->submitted_at->diffForHumans() : null
                    ];
                });
        }

        return response()->json($activities);
    }
}