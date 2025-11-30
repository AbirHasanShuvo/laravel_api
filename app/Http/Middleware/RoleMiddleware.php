<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Un-authorised access'
            ], 403);
        }

        // Split roles if they were passed as a single string with |
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            $roles = explode('|', $roles[0]);
        }

        // Check if user role is allowed
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'status' => 'Failed',
            'message' => 'You are not eligible to perform this operation'
        ], 403);
    }
}
