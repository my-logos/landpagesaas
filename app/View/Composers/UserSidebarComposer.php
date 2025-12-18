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
        // Optimized: Use simple count queries for better performance
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

            // Count abandoned orders (duplicates) - Optimized: Use SQL aggregation
            // Find orders with same product_id and landing_page_id created within 24 hours
            // This approximates duplicate detection using SQL for better performance
            // Exact duplicate detection would require loading all orders, which is expensive
            $duplicateGroups = $user->orders()
                ->where(function ($query) use ($excludedStatuses) {
                    $query->whereNotIn('status', $excludedStatuses)
                        ->whereNotIn('shipping_status', $excludedStatuses);
                })
                ->where('created_at', '>=', now()->subDay())
                ->selectRaw('product_id, landing_page_id, COUNT(*) as duplicate_count')
                ->groupBy('product_id', 'landing_page_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            // Sum duplicate counts (for each group: count - 1 = number of duplicates)
            $abandonedOrdersCount = $duplicateGroups->sum(function ($group) {
                return max(0, $group->duplicate_count - 1);
            });

            // Count new messages and unread customer replies
            // Messages with status 'new' (never read)
            $newStatusMessagesCount = \App\Models\Message::where('user_id', $user->id)
                ->where('status', 'new')
                ->whereNull('read_at')
                ->count();

            // Messages that have unread customer replies (replies from customers after last read)
            // Get messages with customer replies that were created after read_at
            $messagesWithUnreadReplies = \App\Models\Message::where('user_id', $user->id)
                ->whereHas('replies', function ($query) {
                    $query->where('is_customer_reply', true);
                })
                ->with(['replies' => function ($q) {
                    $q->where('is_customer_reply', true)->orderBy('created_at', 'desc');
                }])
                ->get()
                ->filter(function ($message) {
                    // If message was never read, count if it has any customer replies
                    if (!$message->read_at) {
                        return $message->replies->count() > 0;
                    }
                    // If message was read, count if there are customer replies after read_at
                    return $message->replies->where('created_at', '>', $message->read_at)->count() > 0;
                })->count();

            $newMessagesCount = $newStatusMessagesCount + $messagesWithUnreadReplies;

            // Count unread support ticket replies from admin
            $unreadSupportRepliesCount = \App\Models\SupportTicket::where('user_id', $user->id)
                ->whereHas('replies', function ($query) {
                    $query->where('is_admin_reply', true)
                        ->whereNull('user_read_at');
                })
                ->count();
        } else {
            $newMessagesCount = 0;
            $unreadSupportRepliesCount = 0;
        }

        $data['user'] = $user;
        $data['currentRoute'] = $currentRoute;
        $data['userPackageName'] = $userPackageName;
        $data['productsCount'] = $productsCount;
        $data['pagesCount'] = $pagesCount;
        $data['ordersCount'] = $ordersCount;
        $data['abandonedOrdersCount'] = $abandonedOrdersCount;
        $data['newMessagesCount'] = $newMessagesCount ?? 0;
        $data['unreadSupportRepliesCount'] = $unreadSupportRepliesCount ?? 0;

        $view->with($data);
    }
}
