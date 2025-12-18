<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\EnforcesPackageLimits;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\VerifiesResourceOwnership;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;
use App\Mail\OrderStatusUpdate;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    use EnforcesPackageLimits, HasLocaleAndTranslation, VerifiesResourceOwnership;

    public function index(Request $request)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        $user = $request->user();

        // Get performance settings from user database record
        $performanceMode = $user->performance_mode ?? 'fast';
        $includeSessionData = $user->include_session_data ?? false;
        $includeLocation = $user->include_location_data ?? false;
        $includeDeviceType = $user->include_device_type ?? false;

        // Reset to defaults if requested
        if ($request->has('reset_performance')) {
            $performanceMode = 'fast';
            $includeSessionData = false;
            $includeLocation = false;
            $includeDeviceType = false;

            // Update user record
            $user->update([
                'performance_mode' => 'fast',
                'include_session_data' => false,
                'include_location_data' => false,
                'include_device_type' => false,
            ]);
        }

        // Build query
        $query = Order::where('user_id', $user->id)
            ->with(['product', 'product.user']);

        // Add landing page relationship if needed
        if ($performanceMode !== 'fast') {
            $query->with('landingPage');
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('landing_page_id')) {
            $query->where('landing_page_id', $request->input('landing_page_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Get orders based on performance mode
        if ($performanceMode === 'fast') {
            // Basic data only
            $orders = $query->select('id', 'user_id', 'product_id', 'quantity', 'status', 'shipping_status', 'total_cents', 'currency', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Full or selective data - include customer_data for session/location/device info
            $orders = $query->select('id', 'user_id', 'product_id', 'quantity', 'status', 'shipping_status', 'total_cents', 'currency', 'created_at', 'customer_data', 'landing_page_id')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get filter data
        $products = Product::where('user_id', $user->id)->get();
        $pages = \App\Models\Page::where('user_id', $user->id)->get();
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'rejected', 'failed_delivery', 'postponed'];

        // Calculate estimated load time
        $estimatedLoadTime = $this->calculateEstimatedLoadTime($performanceMode, $orders->count());

        // Process customer data for each order
        $orders->each(function ($order) {
            $customerData = $order->customer_data ?? [];
            $order->customer_ip = $customerData['ip'] ?? null;
            $order->customer_session_id = $customerData['session_id'] ?? null;
            $order->customer_city = $customerData['city'] ?? null;
            $order->customer_device_type = $customerData['device_type'] ?? null;
            $order->customer_size = $customerData['size'] ?? null;
            $order->customer_color = $customerData['color'] ?? null;
        });

        return view('user.orders.index', compact(
            'orders',
            'products',
            'pages',
            'statuses',
            'performanceMode',
            'includeSessionData',
            'includeLocation',
            'includeDeviceType',
            'estimatedLoadTime',
            'locale',
            'dir',
            't'
        ));
    }

    /**
     * Update performance settings
     */
    public function updatePerformanceSettings(Request $request)
    {
        $user = $request->user();

        // Update user's performance settings in database
        $user->update([
            'performance_mode' => $request->input('performance_mode', 'fast'),
            'include_session_data' => $request->has('include_session'),
            'include_location_data' => $request->has('include_location'),
            'include_device_type' => $request->has('include_device'),
        ]);

        return redirect()->route('user.orders.index')
            ->with('success', TranslationHelper::get('messages.performance_settings_updated', 'Performance settings updated successfully'));
    }

    /**
     * Export orders data
     */
    public function export(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['product', 'landingPage'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Order ID',
                'Date',
                'Product',
                'Quantity',
                'Total',
                'Currency',
                'Status',
                'Shipping Status',
                'Landing Page'
            ]);

            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->product ? $order->product->name : '-',
                    $order->quantity,
                    $order->total_cents / 100,
                    $order->currency ?? 'EGP',
                    $order->status,
                    $order->shipping_status ?? 'pending',
                    $order->landingPage ? $order->landingPage->title : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate estimated load time based on performance mode
     */
    protected function calculateEstimatedLoadTime(string $mode, int $orderCount): string
    {
        $baseTime = 50; // Base time in ms
        $perOrder = match ($mode) {
            'fast' => 2,
            'selective' => 5,
            'full' => 10,
            default => 2
        };

        $totalTime = $baseTime + ($orderCount * $perOrder);
        $maxTime = $totalTime + ($totalTime * 0.4); // Add 40% variance

        return "{$totalTime}-{$maxTime}ms";
    }

    /**
     * Show abandoned orders page
     */
    public function abandoned(Request $request)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        $user = $request->user();

        // Get all orders for this user
        $allOrders = Order::where('user_id', $user->id)
            ->with(['product', 'landingPage'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Find duplicate orders
        // Duplicate = same product_id, same landing_page_id, created within 24 hours
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

        // Apply filters
        $filterType = $request->input('filter_type', 'all'); // all, duplicates, incomplete
        $search = $request->input('search');

        if ($filterType === 'duplicates') {
            $abandonedOrders = $duplicateOrders;
        } else {
            // For now, we only have duplicates. Incomplete orders would need additional tracking
            $abandonedOrders = $duplicateOrders;
        }

        // Apply search filter
        if ($search) {
            $abandonedOrders = $abandonedOrders->filter(function ($order) use ($search) {
                return stripos((string)$order->id, $search) !== false
                    || ($order->product && stripos($order->product->name, $search) !== false)
                    || ($order->landingPage && stripos($order->landingPage->title, $search) !== false);
            });
        }

        // Mark orders as duplicate
        $abandonedOrders = $abandonedOrders->map(function ($order) use ($duplicateOrders) {
            $order->is_duplicate = $duplicateOrders->contains('id', $order->id);
            $order->duplicate_count = $duplicateOrders->where('product_id', $order->product_id)
                ->where('landing_page_id', $order->landing_page_id)
                ->count();
            return $order;
        });

        $totalCount = $abandonedOrders->count();

        return view('user.orders.abandoned', compact(
            'abandonedOrders',
            'totalCount',
            'filterType',
            'search',
            'locale',
            'dir',
            't'
        ));
    }

    public function create(Request $request)
    {
        $products = Product::where('user_id', $request->user()->id)->get();

        return view('user.orders.form', compact('products'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Check daily and monthly limits
        $this->checkOrderLimits($user);

        // Validate input data
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);

        $data['user_id'] = $user->id;
        $data['total_cents'] = $product->price_cents * $data['quantity'];
        $data['currency'] = $product->currency ?? 'EGP'; // Use product currency
        $data['status'] = 'pending';
        $data['shipping_status'] = 'pending'; // New orders start as pending

        Order::create($data);

        return redirect()->route('user.orders.index')
            ->with('success', TranslationHelper::get('messages.order_created'));
    }

    /**
     * Update shipping status for an order
     */
    public function updateShippingStatus(Request $request, Order $order)
    {
        $this->verifyOwnership($order, $request->user());

        $request->validate([
            'shipping_status' => 'required|in:pending,processing,shipped,delivered,rejected,failed_delivery,postponed',
        ]);

        $oldStatus = $order->shipping_status;
        $newStatus = $request->input('shipping_status');
        $order->shipping_status = $newStatus;
        $order->save();

        // Send webhook event if status changed
        if ($oldStatus !== $newStatus) {
            $this->sendWebhookEvent('order_status_updated', $order);

            // Send email notification to customer if status changed
            $order->load('product');
            $customerEmail = $order->customer_data['email'] ?? null;
            if ($customerEmail) {
                try {
                    Mail::to($customerEmail)->send(new OrderStatusUpdate($order, $oldStatus, $newStatus));
                } catch (\Exception $e) {
                    Log::error('Failed to send order status update email to customer', [
                        'order_id' => $order->id,
                        'customer_email' => $customerEmail,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return redirect()->route('user.orders.index')
            ->with('success', TranslationHelper::get('messages.shipping_status_updated'));
    }

    /**
     * Send webhook event for order status update
     *
     * @param string $eventType
     * @param Order $order
     * @return void
     */
    protected function sendWebhookEvent(string $eventType, Order $order): void
    {
        try {
            // Load relationships if not already loaded
            if (!$order->relationLoaded('product')) {
                $order->load('product');
            }
            if (!$order->relationLoaded('landingPage')) {
                $order->load('landingPage');
            }

            $product = $order->product;
            $page = $order->landingPage;
            $customerData = $order->customer_data ?? [];

            // Prepare webhook payload
            $webhookData = [
                'order_id' => $order->order_number ?? 'ORD-' . $order->id,
                'order_internal_id' => $order->id,
                'customer_name' => $customerData['name'] ?? '',
                'customer_phone' => $customerData['phone'] ?? '',
                'customer_email' => $customerData['email'] ?? '',
                'customer_address' => $customerData['address'] ?? '',
                'product_name' => $product->name ?? '',
                'product_id' => $product->id ?? null,
                'quantity' => $order->quantity ?? 1,
                'total_amount' => ($order->total_cents ?? 0) / 100,
                'currency' => $order->currency ?? 'EGP',
                'landing_page' => $page->title ?? '',
                'landing_page_id' => $page->id ?? null,
                'order_status' => $order->status ?? 'pending',
                'shipping_status' => $order->shipping_status ?? 'pending',
                'updated_at' => $order->updated_at->toIso8601String(),
            ];

            // Add optional fields if available
            if (isset($customerData['size'])) {
                $webhookData['product_size'] = $customerData['size'];
            }
            if (isset($customerData['color'])) {
                $webhookData['product_color'] = $customerData['color'];
            }

            // Send webhook event
            $webhookService = app(WebhookService::class);
            $webhookService->dispatch($eventType, $webhookData, $order->user_id);
        } catch (\Exception $e) {
            // Don't fail the status update if webhook fails
            \Log::error('Webhook event error', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
                'order_id' => $order->id ?? null,
            ]);
        }
    }

    /**
     * Show order statistics page
     */
    public function statistics(Request $request)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        $user = $request->user();

        // Get date filters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Build base query with date filters
        $ordersQuery = $user->orders();
        if ($dateFrom) {
            $ordersQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $ordersQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Get orders statistics
        $ordersStats = $this->getOrdersStatistics($user, $ordersQuery);

        // Get chart data
        $chartData = $this->getChartData($user, $t, $dateFrom, $dateTo);

        return view('user.orders.statistics', array_merge(
            compact('locale', 'dir', 't', 'dateFrom', 'dateTo'),
            $ordersStats,
            $chartData
        ));
    }

    /**
     * Get orders statistics
     */
    protected function getOrdersStatistics($user, $ordersQuery): array
    {
        // Total orders count (all statuses)
        $totalOrders = (int) $ordersQuery->count();

        // Delivered orders query - check both 'status' and 'shipping_status' fields
        $deliveredOrdersQuery = clone $ordersQuery;
        $deliveredOrdersQuery->where(function ($query) {
            $query->where('status', 'delivered')
                ->orWhere('shipping_status', 'delivered');
        });

        // Delivered orders count
        $deliveredOrders = (int) $deliveredOrdersQuery->count();

        // Delivered orders sales amount
        $deliveredOrdersSales = (int) ($deliveredOrdersQuery->sum('total_cents') ?? 0);

        // Completed orders count (orders that are not rejected, failed_delivery, or postponed)
        $completedOrdersQuery = clone $ordersQuery;
        $completedOrders = (int) $completedOrdersQuery
            ->where(function ($query) {
                $query->whereNotIn('status', ['rejected', 'failed_delivery', 'postponed'])
                    ->whereNotIn('shipping_status', ['rejected', 'failed_delivery', 'postponed']);
            })
            ->count();

        // Total sales includes all orders except rejected statuses
        $totalSalesQuery = clone $ordersQuery;
        $totalSales = (int) ($totalSalesQuery
            ->where(function ($query) {
                $query->whereNotIn('status', ['rejected', 'failed_delivery', 'postponed'])
                    ->whereNotIn('shipping_status', ['rejected', 'failed_delivery', 'postponed']);
            })
            ->sum('total_cents') ?? 0);

        // Delivery rate: percentage of delivered orders out of completed orders
        $deliveryRate = $completedOrders > 0
            ? round(($deliveredOrders / $completedOrders) * 100, 2)
            : 0.00;

        return compact('totalOrders', 'deliveredOrders', 'deliveredOrdersSales', 'totalSales', 'deliveryRate');
    }

    /**
     * Get chart data for orders
     */
    protected function getChartData($user, $t, $dateFrom = null, $dateTo = null): array
    {
        // Build base query with date filters
        $baseQuery = $user->orders();
        if ($dateFrom) {
            $baseQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Orders by product
        $ordersByProductQuery = clone $baseQuery;
        $ordersByProduct = $ordersByProductQuery
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
        $ordersByStatus = collect($allStatuses)->map(function ($status) use ($baseQuery) {
            $statusQuery = clone $baseQuery;
            return (object)[
                'status' => $status,
                'count' => $statusQuery->where('status', $status)->count()
            ];
        })->filter(function ($item) {
            return $item->count > 0;
        });

        // Orders by landing page
        $ordersByPageQuery = clone $baseQuery;
        $ordersByPage = $ordersByPageQuery
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
     * Check daily and monthly order limits
     */
    protected function checkOrderLimits($user): void
    {
        $dailyCount = Order::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $this->enforceLimit($user, 'daily_orders_limit', $dailyCount);

        $monthlyCount = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $this->enforceLimit($user, 'monthly_orders_limit', $monthlyCount);
    }
}
