<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Update last activity for authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            try {
                // Всегда обновлять время последней активности при запросе админ-панели
                if ($request->is('admin*')) {
                    $user->updateLastActivity();
                    Log::info('Admin activity updated for user: ' . $user->id);
                }
                // Only update if last activity was more than 5 minutes ago to avoid excessive DB writes
                elseif (! $user->last_activity_at || $user->last_activity_at->diffInMinutes(now()) >= 5) {
                    $user->updateLastActivity();
                }
            } catch (\Exception $e) {
                // Ignore database errors to prevent breaking the application
                Log::warning('Failed to update user activity: '.$e->getMessage());
            }
        }

        return $response;
    }
}
