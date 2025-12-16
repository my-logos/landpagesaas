<?php

namespace App\Http\Controllers\Concerns;

use App\Helpers\TranslationHelper;

trait HasLocaleAndTranslation
{
    /**
     * Get locale, direction, and translation function
     *
     * @return array{locale: string, dir: string, t: callable}
     */
    protected function getLocaleData(): array
    {
        $locale = app()->getLocale();

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
            app()->setLocale('en');
            session(['locale' => 'en']);
        }

        $dir = $locale === 'ar' ? 'rtl' : 'ltr';
        $t = function ($key) {
            return TranslationHelper::get($key);
        };

        return [
            'locale' => $locale,
            'dir' => $dir,
            't' => $t,
        ];
    }

    /**
     * Get translated message with fallback
     *
     * @param string $messageKey
     * @param string $fallback
     * @return string
     */
    protected function getTranslatedMessage(string $messageKey, string $fallback): string
    {
        $translated = TranslationHelper::get($messageKey);
        return $translated !== $messageKey ? $translated : $fallback;
    }
}
