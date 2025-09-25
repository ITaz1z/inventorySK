<?php
// File: app/Http/Middleware/RoleMiddleware.php
// Buat middleware baru untuk cek role

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Cek apakah user role sesuai dengan yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized - Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}