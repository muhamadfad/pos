<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPusatAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403);
        }

        if ($user->canManagePusat()) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke Logistik Pusat.');
    }
}