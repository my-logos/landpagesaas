<?php

namespace App\Services;

use App\Models\Settings;

class SettingsService
{
    /**
     * Default settings values
     */
    private const DEFAULTS = [
        'site_name' => 'DropSaas',
        'site_logo' => null,
        'site_email' => 'info@DropSaas.com',
        'site_phone' => '+123456789',
        'seo_title' => 'DropSaas - Create Professional Landing Pages',
        'seo_description' => 'Enjoy creating simple and effective landing pages',
        'seo_keywords' => 'landing pages, e-commerce, saas',
        'currency_code' => 'USD',
        'default_language' => 'en',
    ];

    /**
     * Get setting value with default fallback
     *
     * @param string $key
     * @param mixed $default
     * @param string|null $locale
     * @return mixed
     */
    public function get(string $key, $default = null, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $value = Settings::where('key', $key)
            ->where('locale', $locale)
            ->value('value');

        if ($value === null) {
            // Try default language (en)
            if ($locale !== 'en') {
                $value = Settings::where('key', $key)
                    ->where('locale', 'en')
                    ->value('value');
            }

            if ($value === null) {
                return $default ?? (self::DEFAULTS[$key] ?? null);
            }
        }

        return $value;
    }

    /**
     * Set setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $locale
     * @return void
     */
    public function set(string $key, $value, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();

        Settings::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            ['value' => $value]
        );
    }

    /**
     * Get all settings with defaults
     *
     * @param string|null $locale
     * @return array
     */
    public function all(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        $settings = Settings::where('locale', $locale)
            ->pluck('value', 'key')
            ->toArray();

        // Fill missing settings with defaults
        foreach (self::DEFAULTS as $key => $default) {
            if (!isset($settings[$key])) {
                // Try to get from default locale (en)
                if ($locale !== 'en') {
                    $enValue = Settings::where('key', $key)
                        ->where('locale', 'en')
                        ->value('value');
                    if ($enValue !== null) {
                        $settings[$key] = $enValue;
                        continue;
                    }
                }
                $settings[$key] = $default;
            }
        }

        return $settings;
    }

    /**
     * Get site name
     *
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->get('site_name');
    }

    /**
     * Get site logo
     *
     * @return string|null
     */
    public function getSiteLogo(): ?string
    {
        return $this->get('site_logo');
    }

    /**
     * Get site email
     *
     * @return string
     */
    public function getSiteEmail(): string
    {
        return $this->get('site_email');
    }

    /**
     * Get site phone
     *
     * @return string
     */
    public function getSitePhone(): string
    {
        return $this->get('site_phone');
    }

    /**
     * Get SEO title
     *
     * @return string
     */
    public function getSeoTitle(): string
    {
        return $this->get('seo_title');
    }

    /**
     * Get SEO description
     *
     * @return string
     */
    public function getSeoDescription(): string
    {
        return $this->get('seo_description');
    }

    /**
     * Get SEO keywords
     *
     * @return string
     */
    public function getSeoKeywords(): string
    {
        return $this->get('seo_keywords');
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->get('currency_code');
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return $this->get('default_language');
    }
}
