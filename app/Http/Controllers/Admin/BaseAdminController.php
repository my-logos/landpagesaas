<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Base Admin Controller
 * Provides common functionality for all admin controllers
 */
abstract class BaseAdminController extends Controller
{
    use HasLocaleAndTranslation;

    /**
     * Get view data with locale information
     *
     * @param array $additionalData Additional data to include
     * @return array
     */
    protected function getViewData(array $additionalData = []): array
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        return array_merge([
            'locale' => $locale,
            'dir' => $dir,
            't' => $t,
        ], $additionalData);
    }

    /**
     * Redirect with success message
     *
     * @param string $route Route name
     * @param string $messageKey Translation key
     * @param string|null $fallback Fallback message if translation not found
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithSuccess(string $route, string $messageKey, ?string $fallback = null): \Illuminate\Http\RedirectResponse
    {
        $message = $this->getTranslatedMessage($messageKey, $fallback ?? 'Operation completed successfully');
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Redirect with error message
     *
     * @param string $route Route name
     * @param string $messageKey Translation key
     * @param string|null $fallback Fallback message if translation not found
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectWithError(string $route, string $messageKey, ?string $fallback = null): \Illuminate\Http\RedirectResponse
    {
        $message = $this->getTranslatedMessage($messageKey, $fallback ?? 'An error occurred');
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Redirect back with success message
     *
     * @param string $messageKey Translation key
     * @param string|null $fallback Fallback message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBackWithSuccess(string $messageKey, ?string $fallback = null): \Illuminate\Http\RedirectResponse
    {
        $message = $this->getTranslatedMessage($messageKey, $fallback ?? 'Operation completed successfully');
        return redirect()->back()->with('success', $message);
    }

    /**
     * Redirect back with error message
     *
     * @param string $messageKey Translation key
     * @param string|null $fallback Fallback message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBackWithError(string $messageKey, ?string $fallback = null): \Illuminate\Http\RedirectResponse
    {
        $message = $this->getTranslatedMessage($messageKey, $fallback ?? 'An error occurred');
        return redirect()->back()->with('error', $message);
    }

    /**
     * Get translated message with fallback
     *
     * @param string $messageKey Translation key
     * @param string $fallback Fallback message
     * @return string
     */
    protected function getTranslatedMessage(string $messageKey, string $fallback): string
    {
        return TranslationHelper::get($messageKey) ?? $fallback;
    }
}
