<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        // Check if logged-in admin is superadmin
        if (Auth::guard('admin')->user()->role !== 'superadmin') {
            return redirect()->route('admin.dashboard')
                             ->with('error', 'Access denied — Superadmin only.');
        }

        return $next($request);
    }
}
