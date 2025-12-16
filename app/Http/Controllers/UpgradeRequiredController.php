<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;

class UpgradeRequiredController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    public function show(Request $request, string $feature = null)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        
        $user = $request->user();
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];
        
        // Get all packages that have the requested feature
        $packagesWithFeature = SubscriptionPackage::whereJsonContains('features->' . $feature, true)
            ->orderBy('price_cents', 'asc')
            ->get();
        
        // If no feature specified, show general upgrade page
        $featureName = $this->getFeatureName($feature, $locale);
        
        return view('upgrade.required', compact(
            'locale',
            'dir',
            't',
            'user',
            'currentPackage',
            'packagesWithFeature',
            'feature',
            'featureName'
        ));
    }

    protected function getFeatureName(string $feature, string $locale): string
    {
        $features = [
            'facebook_conversion_api' => [
                'ar' => 'Facebook Conversion API',
                'en' => 'Facebook Conversion API',
            ],
        ];

        return $features[$feature][$locale] ?? $feature;
    }
}
