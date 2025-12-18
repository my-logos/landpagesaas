<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Subscription;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Trait to simplify subscription retrieval for users
 * Provides unified methods for subscription handling
 */
trait HasSubscriptionHelper
{
    /**
     * Get package features array for pixel fields
     * 
     * Returns an array indicating which pixel/analytics features are enabled
     * for the user's current package. Used to enable/disable pixel fields in forms.
     * 
     * @param mixed $package The subscription package (can be null)
     * @return array Array with feature keys and boolean values
     */
    protected function getPackageFeatures($package): array
    {
        return [
            'facebook_pixel' => $package ? $package->hasFeature('facebook_pixel') : false,
            'tiktok_pixel' => $package ? $package->hasFeature('tiktok_pixel') : false,
            'snapchat_pixel' => $package ? $package->hasFeature('snapchat_pixel') : false,
            'google_analytics' => $package ? $package->hasFeature('google_analytics') : false,
        ];
    }

    /**
     * Get the active or latest subscription for user
     */
    protected function getUserSubscription(Authenticatable $user = null): ?Subscription
    {
        if ($user === null) {
            $user = request()->user();
        }

        if (!$user) {
            return null;
        }

        // Try to get active subscription first (order by created_at desc to get the latest active)
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->first();

        // If not found, get the latest one (even if expired)
        if (!$subscription) {
            $subscription = Subscription::where('user_id', $user->id)
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return $subscription;
    }

    /**
     * Get the current package for user
     */
    protected function getUserPackage(Authenticatable $user = null)
    {
        $subscription = $this->getUserSubscription($user);
        return $subscription?->package;
    }

    /**
     * Check if user is on free plan
     */
    protected function isUserOnFreePlan(Authenticatable $user = null): bool
    {
        $package = $this->getUserPackage($user);
        return $package && $package->is_free;
    }

    /**
     * Get useful subscription and package data
     */
    protected function getSubscriptionData(Authenticatable $user = null): array
    {
        if ($user === null) {
            $user = request()->user();
        }

        if (!$user) {
            return [
                'subscription' => null,
                'package' => null,
                'is_free' => true,
                'is_active' => false,
            ];
        }

        $subscription = $this->getUserSubscription($user);
        $package = $subscription?->package;

        return [
            'subscription' => $subscription,
            'package' => $package,
            'is_free' => $package && $package->is_free,
            'is_active' => $subscription && $subscription->status === 'active',
        ];
    }
}
