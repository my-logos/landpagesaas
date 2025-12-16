<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookLog;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    protected $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Main webhooks page with tabs
     */
    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $tab = $request->get('tab', 'endpoints');

        // Get all webhooks for statistics
        $webhooks = Webhook::where('user_id', $user->id)->get();

        // Calculate statistics
        $totalWebhooks = $webhooks->count();
        $activeWebhooks = $webhooks->where('is_active', true)->count();
        $totalDeliveries = WebhookLog::whereIn('webhook_id', $webhooks->pluck('id'))->count();
        $successfulDeliveries = WebhookLog::whereIn('webhook_id', $webhooks->pluck('id'))
            ->where('success', true)
            ->count();
        $successRate = $totalDeliveries > 0 
            ? round(($successfulDeliveries / $totalDeliveries) * 100, 2) 
            : 0;

        // Get webhooks for endpoints tab (with relationships)
        $endpoints = Webhook::where('user_id', $user->id)
            ->with(['logs' => function($query) {
                $query->latest('sent_at')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Process last log for each webhook
        $endpoints->each(function ($webhook) {
            $lastLog = $webhook->logs->first();
            $webhook->last_log_sent_at = $lastLog && $lastLog->sent_at ? $lastLog->sent_at : null;
        });

        return view('user.webhooks.index', compact(
            'locale',
            'dir',
            't',
            'tab',
            'webhooks',
            'endpoints',
            'totalWebhooks',
            'activeWebhooks',
            'totalDeliveries',
            'successRate'
        ));
    }

    /**
     * Store a new webhook
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'in:order_received,order_status_updated,merchant_status_updated',
            'secret' => 'nullable|string|max:255',
        ], [
            'name.required' => app()->getLocale() === 'ar' ? 'اسم الـ Webhook مطلوب' : 'Webhook name is required',
            'url.required' => app()->getLocale() === 'ar' ? 'رابط الـ Webhook مطلوب' : 'Webhook URL is required',
            'url.url' => app()->getLocale() === 'ar' ? 'رابط الـ Webhook غير صحيح' : 'Invalid webhook URL',
            'events.required' => app()->getLocale() === 'ar' ? 'يجب اختيار حدث واحد على الأقل' : 'At least one event must be selected',
            'events.min' => app()->getLocale() === 'ar' ? 'يجب اختيار حدث واحد على الأقل' : 'At least one event must be selected',
        ]);

        $webhook = Webhook::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => $validated['secret'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' 
                ? 'تم إنشاء الـ Webhook بنجاح' 
                : 'Webhook created successfully',
            'webhook' => $webhook,
        ]);
    }

    /**
     * Update a webhook
     */
    public function update(Request $request, Webhook $webhook)
    {
        $user = $request->user();

        // Ensure webhook belongs to user
        if ($webhook->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'in:order_received,order_status_updated,merchant_status_updated',
            'secret' => 'nullable|string|max:255',
        ]);

        $webhook->update([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => $validated['secret'] ?? $webhook->secret,
        ]);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' 
                ? 'تم تحديث الـ Webhook بنجاح' 
                : 'Webhook updated successfully',
            'webhook' => $webhook->fresh(),
        ]);
    }

    /**
     * Delete a webhook
     */
    public function destroy(Webhook $webhook)
    {
        $user = request()->user();

        // Ensure webhook belongs to user
        if ($webhook->user_id !== $user->id) {
            abort(403);
        }

        $webhook->delete();

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' 
                ? 'تم حذف الـ Webhook بنجاح' 
                : 'Webhook deleted successfully',
        ]);
    }

    /**
     * Toggle webhook active status
     */
    public function toggle(Webhook $webhook)
    {
        $user = request()->user();

        // Ensure webhook belongs to user
        if ($webhook->user_id !== $user->id) {
            abort(403);
        }

        $webhook->update([
            'is_active' => !$webhook->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' 
                ? 'تم تحديث حالة الـ Webhook بنجاح' 
                : 'Webhook status updated successfully',
            'is_active' => $webhook->is_active,
        ]);
    }

    /**
     * Show a webhook (for editing)
     */
    public function show(Webhook $webhook)
    {
        $user = request()->user();

        // Ensure webhook belongs to user
        if ($webhook->user_id !== $user->id) {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'webhook' => $webhook,
        ]);
    }

    /**
     * Test a webhook
     */
    public function test(Webhook $webhook)
    {
        $user = request()->user();

        // Ensure webhook belongs to user
        if ($webhook->user_id !== $user->id) {
            abort(403);
        }

        // Send test event
        $testData = [
            'order_id' => 'TEST-' . Str::random(8),
            'customer_name' => app()->getLocale() === 'ar' ? 'عميل تجريبي' : 'Test Customer',
            'customer_phone' => '+201234567890',
            'customer_email' => 'test@example.com',
            'product_name' => app()->getLocale() === 'ar' ? 'منتج تجريبي' : 'Test Product',
            'quantity' => 1,
            'total_amount' => 100.00,
            'currency' => 'EGP',
            'landing_page' => app()->getLocale() === 'ar' ? 'صفحة تجريبية' : 'Test Landing Page',
            'order_status' => 'pending',
        ];

        $this->webhookService->sendWebhook($webhook, 'order_received', $testData);

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' 
                ? 'تم إرسال حدث تجريبي بنجاح' 
                : 'Test event sent successfully',
        ]);
    }
}
