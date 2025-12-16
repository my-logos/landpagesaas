<?php

namespace App\Http\Controllers\Admin;

use App\Services\SettingsService;
use App\Http\Controllers\Concerns\HandlesFileUploads;
use App\Models\Language;
use Illuminate\Http\Request;

class SettingsController extends BaseAdminController
{
    use HandlesFileUploads;

    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(Request $request)
    {
        $languages = $this->getActiveLanguages();
        $allLanguages = Language::orderBy('sort_order')->orderBy('name')->get();
        $settingsByLocale = $this->getSettingsByLocale($languages);
        $firstLogo = $this->getFirstLogo($languages, $settingsByLocale);

        return view('admin.settings.index', $this->getViewData(compact(
            'languages',
            'allLanguages',
            'settingsByLocale',
            'firstLogo'
        )));
    }

    public function update(Request $request)
    {
        $validated = $this->validateSettings($request);

        $this->handleLogoUpload($request);
        $this->saveSettings($validated['settings']);

        return $this->redirectWithSuccess(
            'admin.settings.index',
            'messages.settings_updated',
            'Settings updated successfully'
        );
    }

    /**
     * Get active languages with fallback
     */
    private function getActiveLanguages()
    {
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($languages->isEmpty()) {
            return collect([
                (object)['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr'],
                (object)['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl'],
            ]);
        }

        return $languages;
    }

    /**
     * Get settings grouped by locale
     */
    private function getSettingsByLocale($languages): array
    {
        $settingKeys = [
            'site_name',
            'site_email',
            'site_phone',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'currency_code',
            'default_language'
        ];

        $settingsByLocale = [];

        foreach ($languages as $lang) {
            $settingsByLocale[$lang->code] = [];

            foreach ($settingKeys as $key) {
                $settingsByLocale[$lang->code][$key] = $this->settingsService->get($key, '', $lang->code);
            }

            $settingsByLocale[$lang->code]['site_logo'] = $this->settingsService->get('site_logo', '', $lang->code);
        }

        return $settingsByLocale;
    }

    /**
     * Get first available logo
     */
    private function getFirstLogo($languages, $settingsByLocale): ?string
    {
        foreach ($languages as $lang) {
            if (!empty($settingsByLocale[$lang->code]['site_logo'])) {
                return $settingsByLocale[$lang->code]['site_logo'];
            }
        }
        return null;
    }

    /**
     * Validate settings request
     */
    private function validateSettings(Request $request): array
    {
        return $request->validate([
            'settings' => 'required|array',
            'settings.*.locale' => 'required|string',
            'settings.*.site_name' => 'required|string|max:255',
            'settings.*.site_email' => 'required|email|max:255',
            'settings.*.site_phone' => 'nullable|string|max:50',
            'settings.*.seo_title' => 'nullable|string|max:255',
            'settings.*.seo_description' => 'nullable|string|max:500',
            'settings.*.seo_keywords' => 'nullable|string|max:255',
            'settings.*.currency_code' => 'required|string|max:10',
            'settings.*.default_language' => 'required|string|max:10',
        ]);
    }

    /**
     * Handle logo upload
     */
    private function handleLogoUpload(Request $request): void
    {
        $logoUrl = $this->getLogoUrl($request);

        if ($logoUrl) {
            $this->saveLogoForAllLanguages($logoUrl);
        }
    }

    /**
     * Get logo URL from request (file upload or URL input)
     */
    private function getLogoUrl(Request $request): ?string
    {
        if ($request->hasFile('site_logo')) {
            $logoName = $this->uploadImage($request, 'site_logo', 'logos', 'public', 2048);
            return $logoName ? '/storage/logos/' . $logoName : null;
        }

        return $request->input('site_logo_url');
    }

    /**
     * Save logo URL for all active languages
     */
    private function saveLogoForAllLanguages(string $logoUrl): void
    {
        $languages = Language::where('is_active', true)->get();
        foreach ($languages as $lang) {
            $this->settingsService->set('site_logo', $logoUrl, $lang->code);
        }
    }

    /**
     * Save settings for all locales
     */
    private function saveSettings(array $settingsData): void
    {
        foreach ($settingsData as $localeData) {
            $locale = $localeData['locale'];
            foreach ($localeData as $key => $value) {
                if ($key !== 'locale') {
                    $this->settingsService->set($key, $value, $locale);
                }
            }
        }
    }
}
