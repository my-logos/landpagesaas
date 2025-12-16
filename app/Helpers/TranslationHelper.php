<?php

namespace App\Helpers;

use App\Services\SettingsService;

class TranslationHelper
{
    /**
     * Get translation from JSON language files
     * Usage: trans('messages.login') or __('messages.login')
     */
    public static function get($key, $default = null)
    {
        $locale = app()->getLocale();
        $parts = explode('.', $key);

        if (count($parts) < 2) {
            return $default ?? $key;
        }

        $file = $parts[0];
        $messageKey = $parts[1];

        // Special handling for currency - get from settings
        if ($messageKey === 'currency') {
            $settingsService = app(SettingsService::class);
            return $settingsService->getCurrencyCode();
        }

        $path = resource_path("lang/{$locale}/{$file}.json");

        if (!file_exists($path)) {
            // Fallback to English
            $path = resource_path("lang/en/{$file}.json");
        }

        if (!file_exists($path)) {
            return $default ?? $key;
        }

        $translations = json_decode(file_get_contents($path), true);

        return $translations[$messageKey] ?? ($default ?? $key);
    }
}
