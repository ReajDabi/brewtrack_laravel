<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * This runs BEFORE every request to a protected route.
     * It checks if the logged-in user has the correct role.
     *
     * Usage in routes: ->middleware('role:admin')
     * Multiple roles: ->middleware('role:admin,cashier')
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        // Step 1: Is the user even logged in?
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Step 2: Does the user have one of the required roles?
        if (!in_array(auth()->user()->role, $roles)) {
            // If not, show the 403 Access Denied page
            abort(403);
        }

        // Step 3: All good — let the request continue
        return $next($request);
    }
}