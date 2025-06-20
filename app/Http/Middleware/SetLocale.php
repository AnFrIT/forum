<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LanguageController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Supported languages
        $supportedLanguages = ['ru', 'en'];

        // Detect language using LanguageController logic
        $detectedLanguage = LanguageController::detectLanguage($request);

        // Validate detected language
        if (in_array($detectedLanguage, $supportedLanguages)) {
            App::setLocale($detectedLanguage);

            // Store in session for persistence
            if ($request->hasSession() && ! Session::has('locale')) {
                Session::put('locale', $detectedLanguage);
            }
        } else {
            // Fallback to default
            $defaultLocale = config('app.locale', 'ru');
            App::setLocale($defaultLocale);

            if ($request->hasSession()) {
                Session::put('locale', $defaultLocale);
            }
        }

        return $next($request);
    }
}
