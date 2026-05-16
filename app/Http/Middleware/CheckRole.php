<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            // Redirect to user dashboard if trying to access admin pages
            if ($role === 'admin') {
                return redirect()->route('user.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
