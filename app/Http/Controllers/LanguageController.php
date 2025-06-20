<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Supported languages
     */
    private $supportedLanguages = ['ru', 'en'];

    /**
     * Switch application language
     *
     * @param  string  $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $language)
    {
        // Validate language
        if (! in_array($language, $this->supportedLanguages)) {
            return redirect()->back()->with('error', 'Unsupported language');
        }

        // Store language preference in session
        Session::put('locale', $language);
        Session::save(); // Force save session

        // If user is authenticated, save language preference to database
        if (auth()->check()) {
            try {
                auth()->user()->update(['preferred_language' => $language]);
            } catch (\Exception $e) {
                // Ignore database errors for now
            }
        }

        // Set app locale immediately
        App::setLocale($language);

        // Get success message in the new language
        $messages = [
            'ru' => 'Язык успешно изменен',
            'en' => 'Language changed successfully',
        ];

        $message = $messages[$language] ?? $messages['en'];

        // Redirect back with success message
        return Redirect::back()->with('success', $message);
    }

    /**
     * Get current language info
     *
     * @return array
     */
    public function current()
    {
        $locale = App::getLocale();

        $languages = [
            'ru' => [
                'code' => 'ru',
                'name' => 'Russian',
                'native' => 'Русский',
                'flag' => '🇷🇺',
                'rtl' => false,
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇺🇸',
                'rtl' => false,
            ],
        ];

        return response()->json([
            'current' => $languages[$locale] ?? $languages['ru'],
            'available' => $languages,
        ]);
    }

    /**
     * Get language detection middleware
     */
    public static function detectLanguage($request)
    {
        // Priority order for language detection:
        // 1. Session storage (highest priority)
        // 2. User preference (if authenticated)
        // 3. Browser language
        // 4. Default app locale

        $supportedLanguages = ['ru', 'en'];
        $detectedLanguage = config('app.locale', 'ru');

        // Check session first (highest priority)
        if ($request->hasSession() && Session::has('locale')) {
            $sessionLang = Session::get('locale');
            if (in_array($sessionLang, $supportedLanguages)) {
                return $sessionLang;
            }
        }

        // Check user preference
        if (auth()->check() && auth()->user()->preferred_language) {
            $userLang = auth()->user()->preferred_language;
            if (in_array($userLang, $supportedLanguages)) {
                $detectedLanguage = $userLang;
            }
        }
        // Check browser language as fallback
        else {
            $acceptLanguage = $request->header('Accept-Language');
            if ($acceptLanguage) {
                $browserLanguages = explode(',', $acceptLanguage);
                foreach ($browserLanguages as $browserLang) {
                    $lang = substr(trim(explode(';', $browserLang)[0]), 0, 2);
                    if (in_array($lang, $supportedLanguages)) {
                        $detectedLanguage = $lang;
                        break;
                    }
                }
            }
        }

        return $detectedLanguage;
    }
}
