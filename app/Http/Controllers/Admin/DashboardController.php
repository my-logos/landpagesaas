<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Page;
use App\Models\SubscriptionPackage;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends BaseAdminController
{
    public function index(Request $request)
    {
        $viewData = $this->getViewData();
        extract($viewData);

        // Get all statistics
        $stats = $this->getDashboardStatistics();
        $chartData = $this->prepareChartData($stats, $t);

        return view('admin.dashboard', array_merge($viewData, $stats, [
            'chartDataSubscriptions' => $chartData['subscriptions'] ?? [],
            'chartDataPayments' => $chartData['payments'] ?? [],
            'chartDataResources' => $chartData['resources'] ?? [],
        ]));
    }

    /**
     * Get all dashboard statistics
     */
    protected function getDashboardStatistics(): array
    {
        return [
            'totalUsers' => $this->getTotalUsers(),
            'totalPages' => Page::count(),
            'totalProducts' => Product::count(),
            'totalPackages' => SubscriptionPackage::count(),
            'freeSubscriptions' => $this->getSubscriptionStats()['free'],
            'paidSubscriptions' => $this->getSubscriptionStats()['paid'],
            'totalPaid' => $this->getTotalPaid(),
            'totalPending' => $this->getTotalPending(),
        ];
    }

    /**
     * Get total users (excluding admins)
     */
    protected function getTotalUsers(): int
    {
        return User::where('role', '!=', 'admin')->orWhereNull('role')->count();
    }

    /**
     * Get total paid amount (wallet top-ups + package subscriptions)
     * Returns amount in cents
     */
    protected function getTotalPaid(): float
    {
        // Get total wallet top-ups (payments with type='wallet' and status='paid')
        $walletTopUps = Payment::where('type', 'wallet')
            ->where('status', 'paid')
            ->sum('amount') ?? 0;

        // Convert to cents (amount is stored in currency units)
        $walletTopUpsCents = $walletTopUps * 100;

        // Get total package subscription payments (payments with type='package' and status='paid')
        $packagePayments = Payment::where('type', 'package')
            ->where('status', 'paid')
            ->sum('amount') ?? 0;

        // Convert to cents (amount is stored in currency units)
        $packagePaymentsCents = $packagePayments * 100;

        return $walletTopUpsCents + $packagePaymentsCents;
    }

    /**
     * Get total pending amount (subscriptions expiring within 7 days)
     * Returns amount in cents
     */
    protected function getTotalPending(): float
    {
        $sevenDaysFromNow = Carbon::now()->addDays(7);

        // Get active subscriptions that expire within 7 days
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $sevenDaysFromNow)
            ->where('ends_at', '>', Carbon::now())
            ->with('package')
            ->get();

        $totalPending = 0;

        foreach ($expiringSubscriptions as $subscription) {
            // Skip free packages
            if ($subscription->package && !$subscription->package->is_free) {
                // Add package price (already in cents)
                $totalPending += $subscription->package->price_cents;
            }
        }

        return $totalPending;
    }

    /**
     * Get subscription statistics
     */
    private function getSubscriptionStats(): array
    {
        $freePackage = SubscriptionPackage::where('is_free', true)->first();
        $freeSubscriptions = 0;

        if ($freePackage) {
            $freeSubscriptions = Subscription::where('package_id', $freePackage->id)
                ->where('status', 'active')
                ->count();
        }

        $paidSubscriptions = Subscription::whereHas('package', function ($query) {
            $query->where('is_free', false);
        })
            ->where('status', 'active')
            ->count();

        return [
            'free' => $freeSubscriptions,
            'paid' => $paidSubscriptions,
        ];
    }

    /**
     * Prepare chart data
     */
    protected function prepareChartData(array $stats, $t): array
    {
        $totalUsers = $stats['totalUsers'];
        $totalPages = $stats['totalPages'];
        $totalProducts = $stats['totalProducts'];
        $totalPackages = $stats['totalPackages'];
        $freeSubscriptions = $stats['freeSubscriptions'];
        $paidSubscriptions = $stats['paidSubscriptions'];
        $totalPaid = $stats['totalPaid'];
        $totalPending = $stats['totalPending'];

        return [
            'users' => [
                'labels' => [$t('messages.users') ?? 'Users'],
                'data' => [$totalUsers],
                'label' => $t('messages.total_users') ?? 'Total Users',
                'color' => '#3b82f6',
            ],
            'subscriptions' => [
                'labels' => [
                    $t('messages.free_subscriptions') ?? 'Free Subscriptions',
                    $t('messages.paid_subscriptions') ?? 'Paid Subscriptions'
                ],
                'data' => [$freeSubscriptions, $paidSubscriptions],
                'label' => $t('messages.subscriptions') ?? 'Subscriptions',
                'colors' => ['#10b981', '#f59e0b'],
            ],
            'payments' => [
                'labels' => [
                    $t('messages.paid') ?? 'Paid',
                    $t('messages.pending') ?? 'Pending'
                ],
                'data' => [
                    round($totalPaid / 100, 2),
                    round($totalPending / 100, 2)
                ],
                'label' => $t('messages.amounts') ?? 'Amounts',
                'colors' => ['#10b981', '#f59e0b'],
            ],
            'resources' => [
                'labels' => [
                    $t('messages.users') ?? 'Users',
                    $t('messages.pages') ?? 'Pages',
                    $t('messages.products') ?? 'Products',
                    $t('messages.packages') ?? 'Packages'
                ],
                'data' => [$totalUsers, $totalPages, $totalProducts, $totalPackages],
                'label' => $t('messages.resources') ?? 'Resources',
                'colors' => ['#3b82f6', '#8b5cf6', '#ef4444', '#10b981'],
            ],
        ];
    }
}
