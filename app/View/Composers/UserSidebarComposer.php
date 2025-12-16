<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\View\Composers\Concerns\HasLanguageData;

class UserSidebarComposer
{
    use HasLanguageData;

    public function compose(View $view): void
    {
        $data = $this->getLanguageData();

        $user = auth()->user();
        $currentRoute = request()->route()?->getName();

        // Load subscription with package relationship
        // First try to get active subscription
        $userSubscription = $user ? $user->subscription()
            ->where('status', 'active')
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->first() : null;

        // If no active subscription, get the latest one (even if expired)
        if (!$userSubscription && $user) {
            $userSubscription = $user->subscription()
                ->with('package')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $userPackageName = $userSubscription && $userSubscription->package ? $userSubscription->package->name : 'FREE PLAN';

        // Calculate counts for sidebar badges
        $productsCount = $user ? $user->products()->count() : 0;
        $pagesCount = $user ? $user->pages()->count() : 0;

        // Calculate orders count excluding: delivered, postponed, rejected, failed_delivery
        // Check both status and shipping_status fields
        $excludedStatuses = ['delivered', 'postponed', 'rejected', 'failed_delivery'];
        $ordersCount = 0;
        $abandonedOrdersCount = 0;

        if ($user) {
            // Count active orders (excluding delivered, postponed, rejected, failed_delivery)
            $ordersCount = $user->orders()
                ->where(function ($query) use ($excludedStatuses) {
                    $query->whereNotIn('status', $excludedStatuses)
                        ->whereNotIn('shipping_status', $excludedStatuses);
                })
                ->count();

            // Count abandoned orders (duplicates) excluding the same statuses
            $allOrders = $user->orders()
                ->where(function ($query) use ($excludedStatuses) {
                    $query->whereNotIn('status', $excludedStatuses)
                        ->whereNotIn('shipping_status', $excludedStatuses);
                })
                ->with(['product', 'landingPage'])
                ->get();

            // Find duplicate orders (same product_id, same landing_page_id, created within 24 hours)
            $duplicateOrders = collect();
            $processedOrderIds = [];

            foreach ($allOrders as $order) {
                if (in_array($order->id, $processedOrderIds)) {
                    continue;
                }

                $duplicates = $allOrders->filter(function ($o) use ($order) {
                    if ($o->id === $order->id) {
                        return false;
                    }

                    // Same product and landing page
                    $sameProduct = $o->product_id === $order->product_id;
                    $samePage = ($o->landing_page_id === $order->landing_page_id) ||
                        (is_null($o->landing_page_id) && is_null($order->landing_page_id));

                    // Created within 24 hours
                    $timeDiff = abs($o->created_at->diffInHours($order->created_at));

                    return $sameProduct && $samePage && $timeDiff <= 24;
                });

                if ($duplicates->count() > 0) {
                    // Mark all as duplicates
                    $duplicateGroup = collect([$order])->merge($duplicates);
                    $duplicateOrders = $duplicateOrders->merge($duplicateGroup);
                    $processedOrderIds = array_merge($processedOrderIds, $duplicateGroup->pluck('id')->toArray());
                }
            }

            $abandonedOrdersCount = $duplicateOrders->count();
        }

        $data['user'] = $user;
        $data['currentRoute'] = $currentRoute;
        $data['userPackageName'] = $userPackageName;
        $data['productsCount'] = $productsCount;
        $data['pagesCount'] = $pagesCount;
        $data['ordersCount'] = $ordersCount;
        $data['abandonedOrdersCount'] = $abandonedOrdersCount;

        $view->with($data);
    }
}
