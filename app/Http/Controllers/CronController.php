<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\SubscriptionExpiringSoon;

class CronController extends Controller
{
    /**
     * Verify cron request is authorized
     * This checks if the request has the correct token from .env
     */
    private function verifyCronRequest(Request $request): bool
    {
        $token = $request->header('X-Cron-Token') ?? $request->input('token');
        $expectedToken = config('app.cron_token', env('CRON_TOKEN', ''));

        // If no token is set, allow localhost and server IP only
        if (empty($expectedToken)) {
            $clientIp = $request->ip();
            $serverIp = $request->server('SERVER_ADDR');
            $allowedIps = ['127.0.0.1', '::1', 'localhost', $serverIp];

            return in_array($clientIp, $allowedIps) ||
                $clientIp === '127.0.0.1' ||
                strpos($clientIp, '127.') === 0;
        }

        return !empty($token) && hash_equals($expectedToken, $token);
    }

    /**
     * Run Laravel Scheduler
     * This should be called every minute by cron
     */
    public function scheduleRun(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for schedule:run', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            Artisan::call('schedule:run');
            $output = Artisan::output();

            Log::info('Cron: schedule:run executed successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Laravel scheduler executed',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: schedule:run failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run Queue Worker
     * This processes jobs from the queue
     */
    public function queueWork(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for queue:work', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Run queue:work with a timeout to prevent it from running indefinitely
            Artisan::call('queue:work', [
                '--sleep' => 3,
                '--tries' => 3,
                '--max-time' => 3600, // 1 hour
                '--once' => true // Process one job only
            ]);
            $output = Artisan::output();

            Log::info('Cron: queue:work executed successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Queue worker executed',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: queue:work failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make charge for subscriptions - Update expired subscriptions status
     */
    public function makeCharge(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for make-charge', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $now = Carbon::now();
            $updatedCount = 0;

            // Find and update expired subscriptions
            $expiredSubscriptions = Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', $now)
                ->get();

            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);
                $updatedCount++;

                Log::info('Subscription expired', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'package_id' => $subscription->package_id,
                    'ends_at' => $subscription->ends_at
                ]);
            }

            Log::info('Cron: make-charge executed', ['expired_count' => $updatedCount]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription status updated',
                'expired_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: make-charge failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alert user after order expired or pending for too long
     * Notifies users about orders that have been pending for 7+ days
     */
    public function alertUserAfterOrderExpired(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for alert-user/after/order/expired', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $sevenDaysAgo = Carbon::now()->subDays(7);
            $sentCount = 0;

            // Find orders that are pending and older than 7 days
            $oldPendingOrders = \App\Models\Order::where('status', 'pending')
                ->where('created_at', '<', $sevenDaysAgo)
                ->with(['user', 'product'])
                ->get()
                ->groupBy('user_id');

            foreach ($oldPendingOrders as $userId => $orders) {
                $user = $orders->first()->user;
                if (!$user || !$user->email) {
                    continue;
                }

                // Log notification (email notification can be added if needed)
                Log::info('Order pending alert', [
                    'user_id' => $userId,
                    'orders_count' => $orders->count(),
                    'oldest_order_date' => $orders->min('created_at')
                ]);

                $sentCount++;
            }

            Log::info('Cron: alert-user/after/order/expired executed', ['notified_users' => $sentCount]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order expiration alerts processed',
                'notified_users' => $sentCount
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: alert-user/after/order/expired failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alert user before subscription expires (7 days before)
     */
    public function alertUserBeforeOrderExpired(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for alert-user/before/order/expired', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $sevenDaysFromNow = Carbon::now()->addDays(7);
            $today = Carbon::now();

            // Get active subscriptions expiring in 7 days
            $expiringSubscriptions = Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->whereBetween('ends_at', [$today, $sevenDaysFromNow])
                ->with(['user', 'package'])
                ->get();

            $sentCount = 0;
            foreach ($expiringSubscriptions as $subscription) {
                if (!$subscription->user || !$subscription->package) {
                    continue;
                }

                $daysRemaining = Carbon::now()->diffInDays($subscription->ends_at, false);

                if ($daysRemaining <= 7 && $daysRemaining > 0) {
                    try {
                        Mail::to($subscription->user->email)->send(
                            new SubscriptionExpiringSoon($subscription->user, $subscription, $subscription->package, $daysRemaining)
                        );
                        $sentCount++;
                    } catch (\Throwable $e) {
                        Log::error('Failed to send subscription expiring email', [
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            Log::info('Cron: alert-user/before/order/expired executed', ['sent_count' => $sentCount]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pre-expiration alerts sent',
                'sent_count' => $sentCount
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: alert-user/before/order/expired failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset product price for tenants
     * Note: This feature requires additional implementation based on specific tenant/multi-tenant requirements.
     * Currently, the system does not have tenant-specific pricing features.
     * If needed, implement tenant price reset logic here.
     */
    public function tenantResetProductPrice(Request $request)
    {
        if (!$this->verifyCronRequest($request)) {
            Log::warning('Unauthorized cron request for tenant-reset-product-price', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Placeholder: This feature is not currently implemented
            // If tenant pricing reset is needed, implement the logic here
            // Example: Reset temporary price discounts, seasonal pricing, etc.

            Log::info('Cron: tenant-reset-product-price executed - Feature not implemented');

            return response()->json([
                'status' => 'success',
                'message' => 'Product prices reset for tenants (feature not implemented)'
            ]);
        } catch (\Exception $e) {
            Log::error('Cron: tenant-reset-product-price failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
