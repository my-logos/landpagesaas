<?php

namespace App\Http\Controllers\Admin;

use App\Models\FacebookConversionAPISetting;
use Illuminate\Http\Request;

class FacebookConversionAPIController extends BaseAdminController
{
    public function index(Request $request)
    {
        // Get system default settings (for system landing page)
        $settings = FacebookConversionAPISetting::getSystemDefault();
        $auth = auth()->user()->isAdmin();
        if (!$settings) {
            $settings = FacebookConversionAPISetting::create([
                'user_id' => null,
                'is_enabled' => false,
                'is_system_default' => true,
                'currency' => 'EGP',
            ]);
        }

        $isConnected = $settings->isConnected();

        return view('admin.facebook-conversion-api.index', $this->getViewData([
            'fbConversionAPISettings' => $settings,
            'isConnected' => $isConnected,
        ]));
    }

    public function update(Request $request)
    {
        // Validate only if enabled
        $rules = [
            'is_enabled' => 'nullable|boolean',
            'pixel_id' => 'nullable|string|max:255',
            'access_token' => 'nullable|string',
            'test_event_code' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
        ];

        // If enabling, require pixel_id and access_token
        if ($request->boolean('is_enabled')) {
            $rules['pixel_id'] = 'required|string|max:255';
            $rules['access_token'] = 'required|string';
        }

        $validated = $request->validate($rules, [
            'pixel_id.required' => app()->getLocale() === 'ar' ? 'Pixel ID مطلوب عند التفعيل' : 'Pixel ID is required when enabled',
            'access_token.required' => app()->getLocale() === 'ar' ? 'Access Token مطلوب عند التفعيل' : 'Access Token is required when enabled',
        ]);

        $settings = FacebookConversionAPISetting::getSystemDefault();

        if (!$settings) {
            $settings = FacebookConversionAPISetting::create([
                'user_id' => null,
                'is_enabled' => false,
                'is_system_default' => true,
                'currency' => 'EGP',
            ]);
        }

        // Update system default settings
        // These settings are linked to the main system landing page
        $isEnabled = $request->boolean('is_enabled');

        $settings->update([
            'is_enabled' => $isEnabled,
            'pixel_id' => $request->input('pixel_id', ''),
            'access_token' => $request->input('access_token', ''),
            'test_event_code' => $request->input('test_event_code'),
            'currency' => $request->input('currency', 'EGP'),
        ]);

        // Refresh the model to ensure we have the latest data from database
        $settings->refresh();

        return $this->redirectWithSuccess('admin.facebook-conversion-api.index', 'messages.settings_updated');
    }
}
