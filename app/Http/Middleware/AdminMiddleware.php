<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', __('Please login to access this area.'));
        }

        $user = auth()->user();

        // Check if user is banned
        if ($user->isBanned()) {
            auth()->logout();

            return redirect()->route('login')->with('error', __('Your account has been banned.'));
        }

        // Check if user has admin privileges
        if (! $user->canAccessAdminPanel()) {
            abort(403, __('Access denied. Administrator privileges required.'));
        }

        // Update user activity
        $user->updateLastActivity();

        return $next($request);
    }
}
