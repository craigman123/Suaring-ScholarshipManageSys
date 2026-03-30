<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $roleId)
    {
        if (!$request->user() || $request->user()->role_id != $roleId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
