<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            try {
                // Check if user is banned
                if (method_exists($user, 'isBanned') && $user->isBanned()) {
                    // Log out banned user
                    Auth::logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $banMessage = __('Your account has been banned.');

                    if ($user->ban_reason) {
                        $banMessage .= ' '.__('Reason: :reason', ['reason' => $user->ban_reason]);
                    }

                    if ($user->ban_expires_at) {
                        $banMessage .= ' '.__('Ban expires: :date', ['date' => $user->ban_expires_at->format('d.m.Y H:i')]);
                    } else {
                        $banMessage .= ' '.__('This is a permanent ban.');
                    }

                    return Redirect::route('login')->with('error', $banMessage);
                }
            } catch (\Exception $e) {
                // Log error but don't break the application
                \Log::warning('Error checking banned status: '.$e->getMessage());
            }
        }

        return $next($request);
    }
}
