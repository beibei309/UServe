<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        // Check if logged-in admin is superadmin
        if (!$admin || $admin->ha_role !== 'superadmin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied — Superadmin only.',
                ], 403);
            }

            return redirect()->route('admin.dashboard')
                             ->with('error', 'Access denied — Superadmin only.');
        }

        return $next($request);
    }
}
