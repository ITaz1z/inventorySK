<?php
// File: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\PermintaanHeader;
use App\Models\PurchaseOrder;
use App\Models\PermintaanActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'recentActivities' => []
        ];

        // Data berdasarkan role
        switch (true) {
            case $user->isAdminGudang():
                $data['total_permintaan'] = PermintaanHeader::where('user_id', $user->id)->count();
                $data['permintaan_draft'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'draft')->count();
                $data['permintaan_pending'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'pending')->count();
                $data['permintaan_approved'] = PermintaanHeader::where('user_id', $user->id)
                    ->whereIn('status', ['approved', 'partial'])->count();
                $data['permintaan_rejected'] = PermintaanHeader::where('user_id', $user->id)
                    ->where('status', 'rejected')->count();
                
                // Recent permintaan
                $data['recent_permintaan'] = PermintaanHeader::where('user_id', $user->id)
                    ->with('items')
                    ->latest()
                    ->limit(5)
                    ->get();
                
                // Activities
                $data['recentActivities'] = PermintaanActivity::whereHas('permintaanHeader', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->with(['user', 'permintaanHeader'])
                  ->latest()
                  ->limit(10)
                  ->get();
                break;

            case $user->isPurchasing():
                $data['total_permintaan_masuk'] = PermintaanHeader::whereIn('status', ['pending', 'review'])->count();
                $data['permintaan_pending'] = PermintaanHeader::where('status', 'pending')->count();
                $data['permintaan_review'] = PermintaanHeader::where('status', 'review')->count();
                $data['total_po_dibuat'] = PurchaseOrder::where('purchasing_user_id', $user->id)->count();
                $data['po_draft'] = PurchaseOrder::where('purchasing_user_id', $user->id)
                    ->where('status', 'draft')->count();
                $data['po_sent'] = PurchaseOrder::where('purchasing_user_id', $user->id)
                    ->where('status', 'sent')->count();
                
                // Permintaan yang perlu direview
                $data['need_review'] = PermintaanHeader::where('status', 'pending')
                    ->with(['user', 'items'])
                    ->latest()
                    ->limit(5)
                    ->get();
                
                // Recent PO
                $data['recent_po'] = PurchaseOrder::where('purchasing_user_id', $user->id)
                    ->with('permintaanHeader')
                    ->latest()
                    ->limit(5)
                    ->get();
                
                // Activities dari semua permintaan
                $data['recentActivities'] = PermintaanActivity::with(['user', 'permintaanHeader'])
                    ->whereIn('activity_type', ['submitted', 'status_changed', 'po_created'])
                    ->latest()
                    ->limit(10)
                    ->get();
                break;

            case $user->isManager():
                $data['total_permintaan'] = PermintaanHeader::count();
                $data['permintaan_pending'] = PermintaanHeader::whereIn('status', ['pending', 'review'])->count();
                $data['permintaan_approved'] = PermintaanHeader::whereIn('status', ['approved', 'partial'])->count();
                $data['permintaan_urgent'] = PermintaanHeader::where('tingkat_prioritas', 'urgent')
                    ->whereIn('status', ['pending', 'review'])
                    ->count();
                $data['total_po'] = PurchaseOrder::count();
                $data['po_sent'] = PurchaseOrder::where('status', 'sent')->count();
                
                // Urgent items yang perlu perhatian
                $data['urgent_items'] = PermintaanHeader::where('tingkat_prioritas', 'urgent')
                    ->whereIn('status', ['pending', 'review'])
                    ->with(['user', 'items'])
                    ->latest()
                    ->limit(5)
                    ->get();
                
                // Overview by kategori
                $data['by_kategori'] = [
                    'umum' => PermintaanHeader::whereHas('items', function($q) {
                        $q->where('kategori', 'umum');
                    })->count(),
                    'sparepart' => PermintaanHeader::whereHas('items', function($q) {
                        $q->where('kategori', 'sparepart');
                    })->count()
                ];
                
                // All recent activities
                $data['recentActivities'] = PermintaanActivity::with(['user', 'permintaanHeader'])
                    ->latest()
                    ->limit(15)
                    ->get();
                break;
        }

        return view('dashboard.index', $data);
    }

    // API endpoint untuk dashboard stats (AJAX)
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [];
        
        if ($user->isAdminGudang()) {
            $stats = [
                'total' => PermintaanHeader::where('user_id', $user->id)->count(),
                'draft' => PermintaanHeader::where('user_id', $user->id)->where('status', 'draft')->count(),
                'pending' => PermintaanHeader::where('user_id', $user->id)->where('status', 'pending')->count(),
                'approved' => PermintaanHeader::where('user_id', $user->id)->whereIn('status', ['approved', 'partial'])->count(),
            ];
        } elseif ($user->isPurchasing()) {
            $stats = [
                'need_review' => PermintaanHeader::where('status', 'pending')->count(),
                'in_review' => PermintaanHeader::where('status', 'review')->count(),
                'my_po' => PurchaseOrder::where('purchasing_user_id', $user->id)->count(),
            ];
        } else {
            $stats = [
                'total_permintaan' => PermintaanHeader::count(),
                'total_po' => PurchaseOrder::count(),
                'urgent' => PermintaanHeader::where('tingkat_prioritas', 'urgent')->whereIn('status', ['pending', 'review'])->count(),
            ];
        }
        
        return response()->json($stats);
    }

    // API endpoint untuk recent activities (AJAX)
    public function getRecentActivities()
    {
        $user = Auth::user();
        
        $query = PermintaanActivity::with(['user', 'permintaanHeader']);
        
        if ($user->isAdminGudang()) {
            $query->whereHas('permintaanHeader', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $activities = $query->latest()->limit(10)->get();
        
        return response()->json([
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->activity_type,
                    'description' => $activity->description,
                    'user' => $activity->user->name,
                    'permintaan' => $activity->permintaanHeader->nomor_permintaan,
                    'time' => $activity->created_at->diffForHumans(),
                    'icon' => $activity->getActivityIcon(),
                ];
            })
        ]);
    }
}