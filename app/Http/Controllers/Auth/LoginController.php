<?php
// File: app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     * Menggunakan name sebagai field login
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only('name', 'password');
    }

    /**
     * The user has been authenticated.
     * Redirect berdasarkan role dan cek status aktif
     */
    protected function authenticated(Request $request, $user)
    {
        // Cek apakah user aktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'name' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        // Redirect berdasarkan role dengan pesan selamat datang
        $welcomeMessage = 'Selamat datang, ' . $user->name . '!';
        
        switch ($user->role) {
            case 'admin_gudang_umum':
                return redirect()->route('dashboard')->with('success', $welcomeMessage . ' (Admin Gudang Umum)');
            
            case 'admin_gudang_sparepart':
                return redirect()->route('dashboard')->with('success', $welcomeMessage . ' (Admin Gudang Sparepart)');
            
            case 'purchasing_1':
            case 'purchasing_2':
                return redirect()->route('dashboard')->with('success', $welcomeMessage . ' (Tim Purchasing)');
            
            case 'general_manager':
                return redirect()->route('dashboard')->with('success', $welcomeMessage . ' (General Manager)');
            
            case 'atasan':
                return redirect()->route('dashboard')->with('success', $welcomeMessage . ' (Atasan)');
            
            default:
                return redirect()->route('dashboard')->with('success', $welcomeMessage);
        }
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('name', 'remember'))
            ->withErrors([
                'name' => 'Nama atau password salah.',
            ]);
    }
}