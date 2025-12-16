<?php

namespace App\View\Composers\Concerns;

use App\Models\Language;
use App\Helpers\TranslationHelper;

trait HasLanguageData
{
    /**
     * Get available languages and validate current locale
     */
    protected function getLanguageData(): array
    {
        $locale = app()->getLocale();

        // Get available languages from database (only active)
        $availableLanguages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Fallback if no languages in DB
        if ($availableLanguages->isEmpty()) {
            $availableLanguages = collect([
                (object)['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr'],
                (object)['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl'],
            ]);
        }

        // Validate current locale
        $validLocale = $availableLanguages->pluck('code')->contains($locale)
            ? $locale
            : $availableLanguages->first()->code;

        if ($validLocale !== $locale) {
            app()->setLocale($validLocale);
            session(['locale' => $validLocale]);
            $locale = $validLocale;
        }

        // Get direction from language model
        $currentLanguage = $availableLanguages->firstWhere('code', $locale);
        $dir = $currentLanguage ? $currentLanguage->direction : 'ltr';

        // Translation helper
        $t = function ($key) {
            return TranslationHelper::get($key);
        };

        // Get next language for toggle (not needed for dropdown, but kept for backward compatibility)
        $currentIndex = $availableLanguages->search(function ($lang) use ($locale) {
            return $lang->code === $locale;
        });
        $nextLanguage = $availableLanguages->get(
            ($currentIndex !== false && $currentIndex < $availableLanguages->count() - 1)
                ? $currentIndex + 1
                : 0
        );

        // Prepare language switcher data
        $currentLanguage = $availableLanguages->firstWhere('code', $locale);
        $hasMultipleLanguages = $availableLanguages->count() > 2;
        $nextLang = $hasMultipleLanguages ? null : ($availableLanguages->firstWhere('code', '!=', $locale) ?? $availableLanguages->first());

        return [
            'locale' => $locale,
            'dir' => $dir,
            't' => $t,
            'availableLanguages' => $availableLanguages,
            'nextLanguage' => $nextLanguage,
            'currentLanguage' => $currentLanguage,
            'hasMultipleLanguages' => $hasMultipleLanguages,
            'nextLang' => $nextLang,
        ];
    }
}
