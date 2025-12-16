<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends BaseAdminController
{
    public function index(Request $request)
    {
        // Show only active languages in the list
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return view('admin.languages.index', $this->getViewData(compact('languages')));
    }

    public function create()
    {
        return view('admin.languages.create', $this->getViewData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'direction' => 'required|in:ltr,rtl',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        // Handle checkbox values (if not present, set to false)
        $validated['is_active'] = $request->has('is_active') && $request->boolean('is_active');
        $validated['is_default'] = $request->has('is_default') && $request->boolean('is_default');

        // If set as default, unset other defaults
        if ($validated['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);

        return $this->redirectWithSuccess('admin.languages.index', 'messages.language_added');
    }

    public function edit(Language $language)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        return view('admin.languages.edit', compact('language', 'locale', 'dir', 't'));
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'direction' => 'required|in:ltr,rtl',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        // Handle checkbox values (if not present, set to false)
        $validated['is_active'] = $request->has('is_active') && $request->boolean('is_active');
        $validated['is_default'] = $request->has('is_default') && $request->boolean('is_default');

        // Prevent deactivating default language
        if (!$validated['is_active'] && $language->is_default) {
            return $this->redirectWithError(
                'admin.languages.index',
                'messages.cannot_deactivate_default_language',
                'Cannot deactivate default language'
            );
        }

        // Prevent deactivating if it's the last active language
        $activeCount = Language::where('is_active', true)
            ->where('id', '!=', $language->id)
            ->count();

        if (!$validated['is_active'] && $activeCount === 0) {
            return $this->redirectWithError(
                'admin.languages.index',
                'messages.cannot_deactivate_last_language',
                'Cannot deactivate last active language'
            );
        }

        $this->handleDefaultLanguage($validated, $language->id);

        $language->update($validated);

        return $this->redirectWithSuccess('admin.languages.index', 'messages.language_updated');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return $this->redirectWithError('admin.languages.index', 'messages.cannot_delete_default_language');
        }

        $activeCount = Language::where('is_active', true)->count();
        if ($activeCount <= 1 && $language->is_active) {
            return $this->redirectWithError('admin.languages.index', 'messages.cannot_delete_last_language');
        }

        $language->delete();

        return $this->redirectWithSuccess('admin.languages.index', 'messages.language_deleted');
    }

    /**
     * Handle default language setting
     */
    private function handleDefaultLanguage(array $validated, ?int $excludeId = null): void
    {
        if ($validated['is_default'] ?? false) {
            $query = Language::where('is_default', true);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $query->update(['is_default' => false]);
        }
    }
}
