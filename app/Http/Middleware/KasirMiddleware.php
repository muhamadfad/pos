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
            abort(403, 'Silakan login terlebih dahulu');
        }

        $role = auth()->user()->role;
        
        // Kasir, Admin, atau Super Admin bisa akses
        if (!in_array($role, ['kasir', 'admin', 'super_admin'])) {
            abort(403, 'Akses khusus kasir');
        }

        return $next($request);
    }
}