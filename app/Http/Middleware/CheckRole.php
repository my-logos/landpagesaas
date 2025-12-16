<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware(['auth','role:admin'])
     * We'll accept param e.g. role:admin
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Block users who are not active (e.g., awaiting payment)
        if (($user->is_active ?? true) === false) {
            abort(403, 'Account not active.');
        }

        if (! $role) {
            return $next($request);
        }

        // If spatie is not yet configured this will fallback to `role` column.
        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole($role)) {
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // fallback to role column check
            }
        }

        $userRole = $user->role ?? 'user';

        // If role matches, allow access
        if ($userRole === $role) {
            return $next($request);
        }

        // If user has no role but trying to access user routes, allow it
        if (empty($user->role) && $role === 'user') {
            // Auto-assign user role
            $user->role = 'user';
            $user->save();
            return $next($request);
        }

        abort(403, 'Access denied. Required role: ' . $role);
    }
}
