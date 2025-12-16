<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Models\SubscriptionPackage;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use App\Helpers\TranslationHelper;

class PackageController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $packages = SubscriptionPackage::orderBy('is_free', 'desc')
            ->orderBy('price_cents', 'asc')
            ->get();

        $subscriptionData = $this->getSubscriptionData($user);
        $currentSubscription = $subscriptionData['subscription'];
        $currentPackage = $subscriptionData['package'];

        return view('user.packages.index', compact(
            'user',
            'packages',
            'currentSubscription',
            'currentPackage',
            'locale',
            'dir',
            't'
        ));
    }

    public function subscribe(Request $request, SubscriptionService $service, SubscriptionPackage $package)
    {
        $user = $request->user();

        if ($package->is_free) {
            return $this->handleFreePackageSubscription($service, $user, $package);
        }

        return $this->handlePaidPackageSubscription();
    }

    /**
     * Handle free package subscription
     */
    protected function handleFreePackageSubscription(SubscriptionService $service, $user, SubscriptionPackage $package)
    {
        $subscription = $service->subscribeUserToPackage($user, $package->id);
        $message = TranslationHelper::get('messages.subscription_created') . $subscription->status;

        return redirect()->route('user.dashboard')->with('success', $message);
    }

    /**
     * Handle paid package subscription
     */
    protected function handlePaidPackageSubscription()
    {
        $message = TranslationHelper::get('messages.please_select_payment_method');
        return redirect()->route('user.packages.index')->with('info', $message);
    }
}
