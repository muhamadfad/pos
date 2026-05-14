<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Silakan login terlebih dahulu');
        }

        $role = auth()->user()->role;
        
        // Admin atau Super Admin bisa akses
        if (!in_array($role, ['admin', 'super_admin'])) {
            abort(403, 'Akses khusus admin');
        }

        return $next($request);
    }
}