<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Http\Controllers\Controller;
use App\Models\FacebookConversionAPISetting;
use Illuminate\Http\Request;

class FacebookConversionAPIController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];
        $hasFacebookConversionAPI = $currentPackage && $currentPackage->hasFeature('facebook_conversion_api');

        $settings = FacebookConversionAPISetting::getForUser($user->id);
        $isConnected = $settings->isConnected();

        return view('user.facebook-conversion-api.index', compact(
            'locale',
            'dir',
            't',
            'user',
            'currentPackage',
            'hasFacebookConversionAPI',
            'settings',
            'isConnected'
        ));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];
        $hasFacebookConversionAPI = $currentPackage && $currentPackage->hasFeature('facebook_conversion_api');

        if (!$hasFacebookConversionAPI) {
            return redirect()->route('upgrade.required', ['feature' => 'facebook_conversion_api']);
        }

        // Validate only if enabled
        $rules = [
            'is_enabled' => 'nullable|boolean',
            'pixel_id' => 'nullable|string|max:255',
            'access_token' => 'nullable|string',
            'test_event_code' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10',
        ];

        // If enabling, require pixel_id and access_token
        if ($request->has('is_enabled') && $request->input('is_enabled')) {
            $rules['pixel_id'] = 'required|string|max:255';
            $rules['access_token'] = 'required|string';
        }

        $validated = $request->validate($rules, [
            'pixel_id.required' => app()->getLocale() === 'ar' ? 'Pixel ID مطلوب عند التفعيل' : 'Pixel ID is required when enabled',
            'access_token.required' => app()->getLocale() === 'ar' ? 'Access Token مطلوب عند التفعيل' : 'Access Token is required when enabled',
        ]);

        $settings = FacebookConversionAPISetting::getForUser($user->id);
        
        // Update settings - هذه الإعدادات مربوطة تلقائياً بجميع صفحات الهبوط لنفس user_id
        $settings->update([
            'is_enabled' => $request->has('is_enabled') ? true : false,
            'pixel_id' => $validated['pixel_id'] ?? null,
            'access_token' => $validated['access_token'] ?? null,
            'test_event_code' => $validated['test_event_code'] ?? null,
            'currency' => $validated['currency'] ?? 'EGP',
        ]);

        return redirect()->route('user.facebook-conversion-api.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث الإعدادات بنجاح. الإعدادات مربوطة تلقائياً بجميع صفحات الهبوط الخاصة بك.' : 'Settings updated successfully. Settings are automatically linked to all your landing pages.');
    }
}
