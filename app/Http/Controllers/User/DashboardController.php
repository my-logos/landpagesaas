<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get orders statistics
        $ordersStats = $this->getOrdersStatistics($user);

        // Get chart data
        $chartData = $this->getChartData($user, $t);

        // Get subscription data
        $subscriptionData = $this->getSubscriptionData($user);
        $currentSubscription = $subscriptionData['subscription'];
        $currentPackage = $subscriptionData['package'];

        // Get resource counts and usage
        $resourceStats = $this->getResourceStatistics($user, $currentPackage);

        return view('user.dashboard', array_merge(
            compact('locale', 'dir', 't'),
            $ordersStats,
            $chartData,
            [
                'walletBalance' => $user->wallet_balance ?? 0,
                'currentSubscription' => $currentSubscription,
                'currentPackage' => $currentPackage,
            ],
            $resourceStats
        ));
    }

    /**
     * Get orders statistics
     */
    protected function getOrdersStatistics($user): array
    {
        // Total orders count (all statuses)
        $totalOrders = (int) $user->orders()->count();

        // Delivered orders query - check both 'status' and 'shipping_status' fields
        // An order is considered delivered if either status or shipping_status is 'delivered'
        $deliveredOrdersQuery = $user->orders()->where(function ($query) {
            $query->where('status', 'delivered')
                ->orWhere('shipping_status', 'delivered');
        });

        // Delivered orders count (orders with 'delivered' status or shipping_status)
        $deliveredOrders = (int) $deliveredOrdersQuery->count();

        // Delivered orders sales amount (sum of total_cents for delivered orders only)
        $deliveredOrdersSales = (int) ($deliveredOrdersQuery->sum('total_cents') ?? 0);

        // Completed orders count (orders that are not rejected, failed_delivery, or postponed)
        // Check both status and shipping_status fields
        $completedOrders = (int) $user->orders()
            ->where(function ($query) {
                $query->whereNotIn('status', ['rejected', 'failed_delivery', 'postponed'])
                    ->whereNotIn('shipping_status', ['rejected', 'failed_delivery', 'postponed']);
            })
            ->count();

        // Total sales includes all orders except rejected statuses (rejected, failed_delivery, postponed)
        // Check both status and shipping_status fields
        $totalSales = (int) ($user->orders()
            ->where(function ($query) {
                $query->whereNotIn('status', ['rejected', 'failed_delivery', 'postponed'])
                    ->whereNotIn('shipping_status', ['rejected', 'failed_delivery', 'postponed']);
            })
            ->sum('total_cents') ?? 0);

        // Delivery rate: percentage of delivered orders out of completed orders (not rejected/failed/postponed)
        // This gives a more accurate delivery rate based on orders that were actually processed
        $deliveryRate = $completedOrders > 0
            ? round(($deliveredOrders / $completedOrders) * 100, 2)
            : 0.00;

        return compact('totalOrders', 'deliveredOrders', 'deliveredOrdersSales', 'totalSales', 'deliveryRate');
    }

    /**
     * Get chart data for orders
     */
    protected function getChartData($user, $t): array
    {
        // Orders by product
        $ordersByProduct = $user->orders()
            ->select('product_id', DB::raw('count(*) as count'))
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'product' => (object)['name' => ($item->product?->name ?? 'Unknown')],
                    'count' => $item->count
                ];
            });

        // Orders by status
        $allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'rejected', 'failed_delivery', 'postponed'];
        $ordersByStatus = collect($allStatuses)->map(function ($status) use ($user) {
            return (object)[
                'status' => $status,
                'count' => $user->orders()->where('status', $status)->count()
            ];
        })->filter(function ($item) {
            return $item->count > 0;
        });

        // Orders by landing page
        $ordersByPage = $user->orders()
            ->select('landing_page_id', DB::raw('count(*) as count'))
            ->whereNotNull('landing_page_id')
            ->groupBy('landing_page_id')
            ->with('landingPage')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'landingPage' => (object)['title' => ($item->landingPage?->title ?? 'Unknown')],
                    'count' => $item->count
                ];
            });

        return [
            'ordersByProduct' => $ordersByProduct,
            'ordersByStatus' => $ordersByStatus,
            'ordersByPage' => $ordersByPage,
            'chartDataByProduct' => [
                'labels' => $ordersByProduct->pluck('product.name')->toArray(),
                'data' => $ordersByProduct->pluck('count')->toArray(),
                'label' => $t('messages.orders')
            ],
            'chartDataByStatus' => [
                'labels' => $ordersByStatus->pluck('status')->values()->toArray(),
                'data' => $ordersByStatus->pluck('count')->values()->toArray()
            ],
            'chartDataByPage' => [
                'labels' => $ordersByPage->pluck('landingPage.title')->toArray(),
                'data' => $ordersByPage->pluck('count')->toArray(),
                'label' => $t('messages.orders')
            ],
        ];
    }

    /**
     * Get resource statistics and usage
     */
    protected function getResourceStatistics($user, $package): array
    {
        $productsCount = $user->products()->count();
        $pagesCount = $user->pages()->count();
        $monthlyOrdersCount = $user->orders()->whereMonth('created_at', now()->month)->count();
        $dailyEditsCount = $user->pages()->whereDate('updated_at', today())->count();

        return [
            'productsCount' => $productsCount,
            'pagesCount' => $pagesCount,
            'monthlyOrdersCount' => $monthlyOrdersCount,
            'dailyEditsCount' => $dailyEditsCount,
            'pagesUsage' => $this->calculateUsage($pagesCount, $package?->pages_limit),
            'productsUsage' => $this->calculateUsage($productsCount, $package?->products_limit),
            'dailyEditsUsage' => $this->calculateUsage($dailyEditsCount, $package?->daily_orders_limit),
            'monthlyOrdersUsage' => $this->calculateUsage($monthlyOrdersCount, $package?->monthly_orders_limit),
        ];
    }

    /**
     * Calculate usage percentage
     */
    protected function calculateUsage(int $current, ?int $limit): float
    {
        if ($limit === null || $limit === 0) {
            return 0;
        }

        return min(100, ($current / $limit) * 100);
    }
}
