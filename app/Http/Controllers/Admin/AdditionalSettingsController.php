<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Models\AdditionalSetting;
use Illuminate\Http\Request;

class AdditionalSettingsController extends BaseAdminController
{
    use HasSubscriptionHelper;

    public function index(Request $request)
    {
        $userId = auth()->id();
        $user = auth()->user();
        $additionalSettings = AdditionalSetting::getForUser($userId);
        
        // Admin always has access to Facebook Conversion API (no subscription needed)
        $isAdmin = $user && $user->isAdmin();
        $hasFacebookConversionAPI = $isAdmin ? true : false;
        
        if (!$isAdmin) {
            $subscriptionData = $this->getSubscriptionData();
            $currentPackage = $subscriptionData['package'];
            $hasFacebookConversionAPI = $currentPackage && $currentPackage->hasFeature('facebook_conversion_api');
        } else {
            $currentPackage = null;
        }

        return view('admin.additional-settings.index', $this->getViewData(compact('additionalSettings', 'userId', 'hasFacebookConversionAPI', 'currentPackage', 'isAdmin')));
    }

    public function update(Request $request)
    {
        $validated = $this->validateSettings($request);
        $userId = auth()->id();
        $user = auth()->user();

        // Admin always has access to Facebook Conversion API (no subscription needed)
        $isAdmin = $user && $user->isAdmin();
        $hasFacebookConversionAPI = $isAdmin ? true : false;

        if (!$isAdmin) {
            // Check if user is trying to enable Facebook Conversion API without proper package
            $subscriptionData = $this->getSubscriptionData();
            $currentPackage = $subscriptionData['package'];
            $hasFacebookConversionAPI = $currentPackage && $currentPackage->hasFeature('facebook_conversion_api');

            if ($request->has('facebook_conversion_api_enabled') && $request->input('facebook_conversion_api_enabled') && !$hasFacebookConversionAPI) {
                return redirect()->route('upgrade.required', ['feature' => 'facebook_conversion_api']);
            }
        }

        $additionalSettings = AdditionalSetting::getForUser($userId);
        $updateData = $this->prepareUpdateData($request, $validated, $additionalSettings, $hasFacebookConversionAPI);

        $additionalSettings->update($updateData);

        return $this->redirectWithSuccess('admin.additional-settings.index', 'messages.settings_updated');
    }

    /**
     * Validate settings input
     */
    private function validateSettings(Request $request): array
    {
        return $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'ai_provider' => 'nullable|string|in:gemini,openai',
            'ai_gemini_api_key' => 'nullable|string',
            'ai_openai_api_key' => 'nullable|string',
            'ai_openai_model' => 'nullable|string|in:gpt-3.5-turbo,gpt-4',
            'facebook_pixel_enabled' => 'nullable',
            'facebook_pixel_id' => 'nullable|string',
            'facebook_conversion_api_enabled' => 'nullable',
            'facebook_conversion_api_access_token' => 'nullable|string',
            'facebook_conversion_api_pixel_id' => 'nullable|string',
            'google_analytics_enabled' => 'nullable',
            'google_analytics_id' => 'nullable|string',
            'tiktok_pixel_enabled' => 'nullable',
            'tiktok_pixel_id' => 'nullable|string',
            'snapchat_pixel_enabled' => 'nullable',
            'snapchat_pixel_id' => 'nullable|string',
            'google_tag_enabled' => 'nullable',
            'google_tag_id' => 'nullable|string',
            'recaptcha_version' => 'nullable|string|in:v2,v3,none',
            'recaptcha_v2_site_key' => 'nullable|string',
            'recaptcha_v2_secret_key' => 'nullable|string',
            'recaptcha_v3_site_key' => 'nullable|string',
            'recaptcha_v3_secret_key' => 'nullable|string',
            'loading_bar_enabled' => 'nullable',
        ]);
    }

    /**
     * Prepare update data
     */
    private function prepareUpdateData(Request $request, array $validated, AdditionalSetting $currentSettings, bool $hasFacebookConversionAPI = false): array
    {
        $updateData = ['user_id' => auth()->id()];
        $updateData = array_merge($updateData, $this->prepareRecaptchaData($request));
        $updateData = array_merge($updateData, $this->prepareOtherFields($validated, $currentSettings));
        
        // Handle all enabled fields (checkboxes)
        $updateData = array_merge($updateData, $this->prepareEnabledFields($request, $hasFacebookConversionAPI));

        return $updateData;
    }

    /**
     * Prepare enabled fields (checkboxes) from request
     */
    private function prepareEnabledFields(Request $request, bool $hasFacebookConversionAPI = false): array
    {
        $enabledFields = [
            'facebook_pixel_enabled',
            'google_analytics_enabled',
            'tiktok_pixel_enabled',
            'snapchat_pixel_enabled',
            'google_tag_enabled',
            'loading_bar_enabled',
        ];

        $updateData = [];

        // Handle all enabled fields
        foreach ($enabledFields as $field) {
            $updateData[$field] = $request->has($field);
        }

        // Handle Facebook Conversion API enabled field
        if ($hasFacebookConversionAPI) {
            $updateData['facebook_conversion_api_enabled'] = $request->has('facebook_conversion_api_enabled');
        } else {
            $updateData['facebook_conversion_api_enabled'] = false;
            // Clear related fields if user doesn't have access
            $updateData['facebook_conversion_api_access_token'] = null;
            $updateData['facebook_conversion_api_pixel_id'] = null;
        }

        return $updateData;
    }

    /**
     * Prepare reCAPTCHA related data
     */
    private function prepareRecaptchaData(Request $request): array
    {
        $recaptchaVersion = $request->input('recaptcha_version', 'none');

        return [
            'recaptcha_version' => $recaptchaVersion,
            'recaptcha_v2_enabled' => $recaptchaVersion === 'v2',
            'recaptcha_v3_enabled' => $recaptchaVersion === 'v3',
        ];
    }

    /**
     * Prepare other fields from validated data
     */
    private function prepareOtherFields(array $validated, AdditionalSetting $currentSettings): array
    {
        $updateData = [];

        foreach ($validated as $key => $value) {
            if ($this->shouldSkipField($key)) {
                continue;
            }

            // For string fields, use empty string if null, otherwise use the value or keep current
            if (is_string($value) || $value === null) {
                $updateData[$key] = $value ?? $currentSettings->{$key} ?? '';
            } else {
                $updateData[$key] = $value ?? $currentSettings->{$key};
            }
        }

        return $updateData;
    }

    /**
     * Check if field should be skipped
     */
    private function shouldSkipField(string $key): bool
    {
        // Skip user_id and _enabled fields (they are handled separately)
        // recaptcha_version is handled in prepareRecaptchaData, but recaptcha keys should NOT be skipped
        return in_array($key, ['user_id']) || str_ends_with($key, '_enabled');
    }
}
