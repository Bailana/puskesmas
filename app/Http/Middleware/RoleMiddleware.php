<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $roleArray = explode('|', $roles);
        if (!in_array(Auth::user()->role, $roleArray)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
