<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionService
{
    /**
     * Subscribe a user to a package. Handles free vs paid logic.
     * Returns the created Subscription instance.
     */
    public function subscribeUserToPackage(User $user, string|int $packageId)
    {
        $package = is_numeric($packageId)
            ? SubscriptionPackage::find($packageId)
            : SubscriptionPackage::where('slug', $packageId)->first();

        if (! $package) {
            throw new ModelNotFoundException('Package not found.');
        }

        // If package is free, activate immediately
        if ($package->is_free) {
            return Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'starts_at' => Carbon::now(),
                'ends_at' => null,
                'status' => 'active',
            ]);
        }

        // Paid package: create pending subscription (payment flow needed)
        return Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'starts_at' => null,
            'ends_at' => null,
            'status' => 'pending',
        ]);
    }
}
