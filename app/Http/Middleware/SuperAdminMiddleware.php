<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Silakan login terlebih dahulu');
        }

        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Akses khusus Super Administrator');
        }

        return $next($request);
    }
}