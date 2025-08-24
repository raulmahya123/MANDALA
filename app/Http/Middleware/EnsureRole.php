<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('role:super_admin')
     *   ->middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Tidak login → 403
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Cek peran. Kalau tidak cocok → 403
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Forbidden');
        }

        // Lolos → lanjutkan request
        return $next($request);
    }
}
