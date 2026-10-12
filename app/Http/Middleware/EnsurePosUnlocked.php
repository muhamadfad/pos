<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsurePosUnlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user doesn't have a PIN, bypass
        if (!$user->pos_pin) {
            return $next($request);
        }
        
        // If unlocked in session, allow
        if (session('pos_unlocked') === true) {
            return $next($request);
        }
        
        // Exclude the verify-pin route itself to avoid infinite redirect
        if ($request->routeIs('pos.verify-pin', 'pos.pin-screen')) {
            return $next($request);
        }
        
        // Store intended URL so we can redirect back after unlock
        session(['url.intended_pos' => $request->fullUrl()]);
        
        return redirect()->route('pos.pin-screen');
    }
}
