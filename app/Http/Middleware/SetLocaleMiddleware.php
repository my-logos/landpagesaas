<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use App\Models\Language;
use Closure;
use Illuminate\Http\Request;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $settingsService = app(SettingsService::class);

        // Get available languages from database
        $availableLanguages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // If no languages in DB, use default en/ar
        if ($availableLanguages->isEmpty()) {
            $availableLanguages = collect([
                (object)['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr'],
                (object)['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl'],
            ]);
        }

        // Priority: 1. Session, 2. Settings default_language, 3. First available language
        $defaultLang = $settingsService->getDefaultLanguage();
        $lang = session('locale', $defaultLang);

        // Validate language - must be in available languages
        $validCodes = $availableLanguages->pluck('code')->toArray();
        if (!in_array($lang, $validCodes)) {
            // Try to use default language if it's available
            if (in_array($defaultLang, $validCodes)) {
                $lang = $defaultLang;
            } else {
                // Use first available language
                $lang = $availableLanguages->first()->code;
            }
        }

        // Set application locale
        app()->setLocale($lang);

        // Ensure session is set
        if (!session()->has('locale') || session('locale') !== $lang) {
            session(['locale' => $lang]);
        }

        return $next($request);
    }
}
