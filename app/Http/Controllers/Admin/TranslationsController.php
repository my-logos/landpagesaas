<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationsController extends BaseAdminController
{
    public function index(Request $request)
    {
        $languages = $this->getActiveLanguages();
        $translations = $this->loadTranslations($languages);
        $allKeys = $this->getAllTranslationKeys($translations);

        return view('admin.translations.index', $this->getViewData(compact('languages', 'translations', 'allKeys')));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'translations' => 'required|array',
            'translations.*' => 'nullable|string',
        ]);

        $languages = $this->getActiveLanguages();

        foreach ($languages as $lang) {
            $this->saveTranslationKey(
                $lang->code,
                $validated['key'],
                $validated['translations'][$lang->code] ?? ''
            );
        }

        return $this->redirectWithSuccess('admin.translations.index', 'messages.translation_added');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.*' => 'nullable|string',
        ]);

        $this->updateTranslationsForAllLanguages($validated['translations']);

        return $this->redirectWithSuccess('admin.translations.index', 'messages.translations_updated');
    }

    /**
     * Update translations for all languages
     */
    protected function updateTranslationsForAllLanguages(array $translationsData): void
    {
        $languages = $this->getActiveLanguages();

        foreach ($languages as $lang) {
            if (!isset($translationsData[$lang->code])) {
                continue;
            }

            $this->updateTranslationsForLanguage($lang->code, $translationsData[$lang->code]);
        }
    }

    /**
     * Update translations for a specific language
     */
    protected function updateTranslationsForLanguage(string $langCode, array $translations): void
    {
        $filePath = $this->getTranslationFilePath($langCode);
        $existingTranslations = $this->loadTranslationFile($filePath);
        $updatedTranslations = array_merge($existingTranslations, $translations);
        $this->saveTranslationFile($filePath, $updatedTranslations);
    }

    /**
     * Get active languages with fallback
     */
    private function getActiveLanguages()
    {
        $languages = Language::where('is_active', true)->orderBy('sort_order')->get();

        if ($languages->isEmpty()) {
            return collect([
                (object)['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
                (object)['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية'],
            ]);
        }

        return $languages;
    }

    /**
     * Load translations for all languages
     */
    private function loadTranslations($languages): array
    {
        $translations = [];

        foreach ($languages as $lang) {
            $filePath = $this->getTranslationFilePath($lang->code);
            $translations[$lang->code] = $this->loadTranslationFile($filePath);
        }

        return $translations;
    }

    /**
     * Get translation file path
     */
    private function getTranslationFilePath(string $langCode): string
    {
        return resource_path("lang/{$langCode}/messages.json");
    }

    /**
     * Load translation file
     */
    private function loadTranslationFile(string $filePath): array
    {
        if (File::exists($filePath)) {
            return json_decode(File::get($filePath), true) ?? [];
        }
        return [];
    }

    /**
     * Save translation file
     */
    private function saveTranslationFile(string $filePath, array $translations): void
    {
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Save translation key
     */
    private function saveTranslationKey(string $langCode, string $key, string $value): void
    {
        $filePath = $this->getTranslationFilePath($langCode);
        $translations = $this->loadTranslationFile($filePath);
        $translations[$key] = $value;
        $this->saveTranslationFile($filePath, $translations);
    }

    /**
     * Get all unique translation keys
     */
    private function getAllTranslationKeys(array $translations): array
    {
        $allKeys = [];

        foreach ($translations as $langTranslations) {
            $allKeys = array_merge($allKeys, array_keys($langTranslations));
        }

        return array_values(array_unique($allKeys));
    }
}
