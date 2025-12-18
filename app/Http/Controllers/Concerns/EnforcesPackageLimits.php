<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;

trait EnforcesPackageLimits
{
    /**
     * Check limit and return redirect with flash message if limit reached (instead of abort)
     *
     * @param  User  $user
     * @param  string  $limitField
     * @param  int  $currentCount
     * @param  string  $redirectRoute
     * @return \Illuminate\Http\RedirectResponse|null Returns redirect if limit reached, null otherwise
     */
    protected function checkLimitWithRedirect(User $user, string $limitField, int $currentCount, string $redirectRoute)
    {
        $subscription = $user->subscription;

        // If no subscription or no package, deny by default for safety
        if (! $subscription || ! $subscription->package) {
            Log::error('No subscription or package', ['user_id' => $user->id]);
            return redirect($redirectRoute)
                ->with('error', TranslationHelper::get('messages.no_active_package', 'No active package'));
        }

        $package = $subscription->package;

        // Check if the limit field exists in the package fillable array
        if (!in_array($limitField, $package->getFillable())) {
            Log::error('Invalid limit field', [
                'limit_field' => $limitField,
                'fillable' => $package->getFillable()
            ]);
            return redirect($redirectRoute)
                ->with('error', TranslationHelper::get('messages.invalid_package_limit', 'Invalid package limit check'));
        }

        // Get the limit value (can be null, 0, or a positive integer)
        // Limit value interpretation:
        // - null: Unlimited (no restrictions)
        // - 0: Feature disabled (access denied)
        // - Positive integer: Maximum allowed count
        $limit = $package->getAttribute($limitField);

        // null means unlimited - allow creation
        if ($limit === null) {
            return null;
        }

        // Convert limit to integer for comparison
        $limit = (int) $limit;

        // If limit is 0, feature is disabled - deny access immediately
        if ($limit === 0) {
            Log::warning('Limit is zero', [
                'current_count' => $currentCount,
                'limit' => $limit
            ]);
            return redirect($redirectRoute)
                ->with('error', TranslationHelper::get('messages.limit_reached', 'Limit reached') . ' (' . TranslationHelper::get('messages.limit', 'Limit') . ': ' . $limit . ')');
        }

        // Check if current count (before creating new item) would exceed the limit
        // Example: If limit=5 and currentCount=5, user already has 5 items, can't create 6th
        // We check >= instead of > because we're checking BEFORE creating the new item
        if ($currentCount >= $limit) {
            Log::warning('Limit reached', [
                'user_id' => $user->id,
                'limit_field' => $limitField,
                'current_count' => $currentCount,
                'limit' => $limit,
                'package_id' => $package->id,
                'package_name' => $package->name
            ]);

            // Convert limit field name to user-friendly format
            // Example: 'pages_limit' -> 'pages' -> 'Pages'
            $limitFieldKey = str_replace('_limit', '', $limitField);
            $limitFieldName = TranslationHelper::get('messages.' . $limitFieldKey, $limitFieldKey);

            // Get localized upgrade message template with placeholders
            // Placeholders: :field (resource name), :current (current count), :limit (max limit)
            $messageTemplate = TranslationHelper::get(
                'messages.limit_reached_upgrade',
                'You have reached the limit for :field (:current/:limit). Please upgrade your plan to create more.'
            );

            // Replace placeholders in message template
            // Example: "You have reached the limit for Pages (5/5). Please upgrade your plan..."
            $message = str_replace(
                [':field', ':current', ':limit'],
                [$limitFieldName, $currentCount, $limit],
                $messageTemplate
            );

            return redirect($redirectRoute)
                ->with('error', $message)
                ->with('upgrade_prompt', true)
                ->with('upgrade_url', route('user.packages.index'));
        }

        return null;
    }

    /**
     * Enforce a numeric package limit for a user.
     *
     * @param  string  $limitField  one of: pages_limit, products_limit, daily_orders_limit, monthly_orders_limit
     * @param  int  $currentCount  current count of the resource the user wants to create
     */
    protected function enforceLimit(User $user, string $limitField, int $currentCount): void
    {
        $subscription = $user->subscription;

        // If no subscription or no package, deny by default for safety
        if (! $subscription || ! $subscription->package) {
            Log::error('No subscription or package', ['user_id' => $user->id]);
            abort(Response::HTTP_FORBIDDEN, TranslationHelper::get('messages.no_active_package'));
        }

        $package = $subscription->package;

        // Check if the limit field exists in the package fillable array
        if (!in_array($limitField, $package->getFillable())) {
            Log::error('Invalid limit field', [
                'limit_field' => $limitField,
                'fillable' => $package->getFillable()
            ]);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, TranslationHelper::get('messages.invalid_package_limit') ?? 'Invalid package limit check');
        }

        // Get the limit value (can be null, 0, or a positive integer)
        // Limit value interpretation:
        // - null: Unlimited (no restrictions) - allow creation
        // - 0: Feature disabled (access denied) - abort with 403
        // - Positive integer: Maximum allowed count - check if exceeded
        $limit = $package->getAttribute($limitField);

        // null means unlimited - allow creation, no need to check
        if ($limit === null) {
            return;
        }

        // Convert limit to integer for comparison
        $limit = (int) $limit;

        // If limit is 0, feature is disabled - deny access with 403 Forbidden
        if ($limit === 0) {
            $message = TranslationHelper::get('messages.limit_reached', 'Limit reached');
            Log::warning('Limit is zero', [
                'current_count' => $currentCount,
                'limit' => $limit
            ]);
            abort(Response::HTTP_FORBIDDEN, $message . ' (' . TranslationHelper::get('messages.limit') . ': ' . $limit . ')');
        }

        // Check if current count (before creating new item) would exceed the limit
        // Logic: We check BEFORE creation, so if currentCount >= limit, deny
        // Example: limit=5, currentCount=5 means user already has 5 items
        //          Creating a 6th item would exceed the limit, so we deny
        // Example: limit=1, currentCount=1 means user already has 1 page, can't create another
        if ($currentCount >= $limit) {
            $message = TranslationHelper::get('messages.limit_reached', 'Limit reached');
            Log::warning('Limit reached', [
                'user_id' => $user->id,
                'limit_field' => $limitField,
                'current_count' => $currentCount,
                'limit' => $limit,
                'package_id' => $package->id,
                'package_name' => $package->name
            ]);
            abort(Response::HTTP_FORBIDDEN, $message . ' (' . TranslationHelper::get('messages.limit') . ': ' . $limit . ')');
        }
    }
}
