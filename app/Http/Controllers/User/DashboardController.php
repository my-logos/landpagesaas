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

        // Get subscription data first (needed by other methods)
        $subscriptionData = $this->getSubscriptionData($user);
        $currentSubscription = $subscriptionData['subscription'];
        $currentPackage = $subscriptionData['package'];

        // Get resource counts and usage (optimized: used by tipsSteps)
        $resourceStats = $this->getResourceStatistics($user, $currentPackage);

        // Get orders statistics
        $ordersStats = $this->getOrdersStatistics($user);

        // Get chart data
        $chartData = $this->getChartData($user, $t);

        // Check tips visibility status
        $tipsDisabled = $user->tips_disabled ?? false;

        // Check tips steps completion
        // Optimized: Pre-fetch counts to avoid multiple queries
        $productsCount = $resourceStats['productsCount'] ?? 0;
        $publishedPagesCount = $user->pages()->where('status', 'published')->count();
        $hasVerifiedEmail = $user->hasVerifiedEmail();

        $tipsSteps = [
            'step1' => [
                'completed' => $hasVerifiedEmail,
                'title' => $locale === 'ar' ? 'تفعيل الإيميل' : 'Verify Email',
                'description' => $locale === 'ar' ? 'قم بتفعيل إيميلك للبدء في استخدام المنصة. افتح الإيميل الذي تم إرساله إليك وانقر على رابط التفعيل.' : 'Verify your email to start using the platform. Open the email sent to you and click the verification link.',
                'action_url' => $hasVerifiedEmail ? '#' : (route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) ?? '#'),
                'action_text' => $locale === 'ar' ? 'تفعيل الإيميل' : 'Verify Email',
                'image' => asset('assets/1.png'),
            ],
            'step2' => [
                'completed' => $productsCount > 0,
                'title' => $locale === 'ar' ? 'إنشاء المنتج' : 'Create Product',
                'description' => $locale === 'ar' ? 'قم بإنشاء منتجك الأول وإضافة بياناته مثل الاسم والوصف والسعر والصور' : 'Create your first product and add its data such as name, description, price, and images',
                'action_url' => route('user.products.create'),
                'action_text' => $locale === 'ar' ? 'إنشاء منتج' : 'Create Product',
                'image' => asset('assets/2.png'),
            ],
            'step3' => [
                'completed' => $publishedPagesCount > 0,
                'title' => $locale === 'ar' ? 'إنشاء صفحة هبوط ونشرها' : 'Create & Publish Landing Page',
                'description' => $locale === 'ar' ? 'قم بإنشاء صفحة هبوط لنشر منتجك، ثم انشرها وأخذ رابط الصفحة لمشاركته مع عملائك' : 'Create a landing page to promote your product, then publish it and get the page link to share with your customers',
                'action_url' => route('user.pages.create'),
                'action_text' => $locale === 'ar' ? 'إنشاء صفحة' : 'Create Page',
                'images' => [
                    asset('assets/3.png'),
                    asset('assets/4.png'),
                    asset('assets/5.png'),
                ],
            ],
        ];

        return view('user.dashboard', array_merge(
            compact('locale', 'dir', 't'),
            $ordersStats,
            $chartData,
            [
                'walletBalance' => $user->wallet_balance ?? 0,
                'currentSubscription' => $currentSubscription,
                'currentPackage' => $currentPackage,
                'tipsDisabled' => $tipsDisabled,
                'tipsSteps' => $tipsSteps,
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

        // Orders by status - Optimized: use single query with groupBy instead of N+1 queries
        $ordersByStatusRaw = $user->orders()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'status' => $item->status,
                    'count' => (int) $item->count
                ];
            });

        // Filter out zero counts and sort by status
        $ordersByStatus = $ordersByStatusRaw->filter(function ($item) {
            return $item->count > 0;
        })->values();

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

    /**
     * Disable tips for user
     */
    public function disableTips(Request $request)
    {
        $user = $request->user();

        $user->tips_disabled = $request->input('disabled', true);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tips preference saved'
        ]);
    }
}
