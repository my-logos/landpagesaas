<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use App\View\Composers\Concerns\HasLanguageData;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\SupportTicket;

class AdminSidebarComposer
{
    use HasLanguageData;

    public function compose(View $view): void
    {
        $data = $this->getLanguageData();

        $user = auth()->user();
        $currentRoute = Route::currentRouteName() ?? '';

        // Get count of pending subscriptions with bank transfer payments
        $pendingSubscriptionsCount = Subscription::where('status', 'pending')
            ->whereHas('payment', function ($query) {
                $query->where('payment_method', 'bank_transfer')
                    ->where('status', 'pending');
            })
            ->count();

        // Get count of pending wallet top-up payments with bank transfer
        $pendingWalletPaymentsCount = Payment::where('type', 'wallet')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->count();

        // Total pending count
        $totalPendingCount = $pendingSubscriptionsCount + $pendingWalletPaymentsCount;

        // Count unread support tickets and replies
        // New tickets (never read by admin)
        $unreadTicketsCount = SupportTicket::whereNull('admin_read_at')
            ->count();

        // Tickets with new user replies (replies created after admin_read_at or if ticket was never read)
        $ticketsWithNewReplies = SupportTicket::whereHas('replies', function ($query) {
            $query->where('is_admin_reply', false);
        })
            ->get()
            ->filter(function ($ticket) {
                // If ticket was never read, check if it has any user replies
                if (!$ticket->admin_read_at) {
                    return $ticket->replies()
                        ->where('is_admin_reply', false)
                        ->count() > 0;
                }
                // If ticket was read, check if there are user replies after admin_read_at
                return $ticket->replies()
                    ->where('is_admin_reply', false)
                    ->where('created_at', '>', $ticket->admin_read_at)
                    ->count() > 0;
            })->count();

        $unreadSupportCount = $unreadTicketsCount + $ticketsWithNewReplies;

        $data['user'] = $user;
        $data['currentRoute'] = $currentRoute;
        $data['pendingSubscriptionsCount'] = $totalPendingCount;
        $data['unreadSupportCount'] = $unreadSupportCount;

        $view->with($data);
    }
}
