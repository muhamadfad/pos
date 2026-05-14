<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $role = $user->role;
        
        // Kasir, Admin, atau Super Admin bisa akses route kasir
        if (!in_array($role, ['kasir', 'admin', 'super_admin'])) {
            abort(403, 'Akses ditolak. Anda bukan kasir.');
        }

        // Jika user adalah kasir, batasi akses ke route tertentu
        if ($role === 'kasir') {
            $currentPath = $request->path();
            
            // Route yang diizinkan untuk kasir
            $allowedPaths = [
                'kasir',           // prefix kasir
                'kasir/*',         // semua route di bawah kasir
                'pos',             // POS
                'pos/*',           // semua route di bawah pos
                'profile',         // profile
                'profile/*',       // edit profile
                'logout',          // logout
            ];
            
            $isAllowed = false;
            
            // Cek apakah path saat ini diizinkan
            foreach ($allowedPaths as $allowed) {
                if ($request->is($allowed)) {
                    $isAllowed = true;
                    break;
                }
            }
            
            // Jika tidak diizinkan, redirect ke halaman kasir
            if (!$isAllowed) {
                return redirect()->route('kasir.shift.index');
            }
        }

        return $next($request);
    }
}