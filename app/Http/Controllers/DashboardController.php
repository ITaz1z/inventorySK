<?php
// File: app/Http/Controllers/DashboardController.php
// Copy ini ke file app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\PermintaanBarang;
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
                $data['total_permintaan'] = PermintaanBarang::where('user_id', $user->id)->count();
                $data['permintaan_pending'] = PermintaanBarang::where('user_id', $user->id)
                    ->where('status', 'pending')->count();
                $data['permintaan_approved'] = PermintaanBarang::where('user_id', $user->id)
                    ->where('status', 'approved')->count();
                break;

            case 'purchasing_1':
            case 'purchasing_2':
                $data['total_permintaan_masuk'] = PermintaanBarang::where('status', 'pending')->count();
                $data['total_po_dibuat'] = PurchaseOrder::where('purchasing_user_id', $user->id)->count();
                $data['po_draft'] = PurchaseOrder::where('purchasing_user_id', $user->id)
                    ->where('status', 'draft')->count();
                break;

            case 'general_manager':
            case 'atasan':
                $data['total_permintaan'] = PermintaanBarang::count();
                $data['total_po'] = PurchaseOrder::count();
                $data['permintaan_pending'] = PermintaanBarang::where('status', 'pending')->count();
                break;
        }

        return view('dashboard.index', $data);
    }
}