<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use App\View\Composers\Concerns\HasLanguageData;
use App\Models\Subscription;
use App\Models\Payment;

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

        $data['user'] = $user;
        $data['currentRoute'] = $currentRoute;
        $data['pendingSubscriptionsCount'] = $totalPendingCount;

        $view->with($data);
    }
}
