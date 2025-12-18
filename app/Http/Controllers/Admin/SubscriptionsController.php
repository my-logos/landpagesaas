<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Payment;
use App\Models\SubscriptionPackage;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SubscriptionActivated;
use App\Mail\SubscriptionRenewed;
use App\Mail\NewSubscriptionNotification;
use App\Mail\PaymentReceived;
use App\Mail\WalletTopupNotification;

class SubscriptionsController extends BaseAdminController
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::with(['user', 'package', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get all pending wallet payments (bank transfer)
        $pendingWalletPayments = Payment::where('type', 'wallet')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.subscriptions.index', $this->getViewData(compact('subscriptions', 'pendingWalletPayments')));
    }

    public function show(User $user)
    {
        $subscriptions = $this->getUserSubscriptions($user);
        $currentSubscription = $this->getCurrentSubscription($user);
        $userStats = $this->getUserStats($user);

        // Get pending subscriptions with bank transfer payments
        $pendingSubscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['package', 'payment'])
            ->get();

        // Get pending wallet payments for this user
        $pendingWalletPayments = Payment::where('user_id', $user->id)
            ->where('type', 'wallet')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Merge userStats into view data (extract array values)
        $viewData = array_merge(
            compact('user', 'subscriptions', 'currentSubscription', 'pendingSubscriptions', 'pendingWalletPayments'),
            $userStats
        );

        return view('admin.subscriptions.show', $this->getViewData($viewData));
    }

    /**
     * Approve a pending subscription
     */
    public function approve(Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.subscription_not_pending', 'Subscription is not pending'));
        }

        $payment = $subscription->payment;

        if (!$payment || $payment->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.payment_not_found', 'Payment not found'));
        }

        // Update payment status
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Activate subscription
        $package = $subscription->package;
        $this->activateSubscription($subscription, $package, $payment);

        // Activate user account if not already active
        $user = $subscription->user;
        if (!$user->is_active) {
            $user->is_active = true;
            $user->save();
        }

        // Process wallet deduction if used
        $this->processWalletDeduction($payment, $package);

        // Create invoice
        $this->createPackageInvoice($payment, $package);

        // Create transaction
        $this->createPackageTransaction($payment, $package);

        // Send emails
        try {
            // Check if this is a renewal
            $isRenewal = Subscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'expired')
                ->exists();

            if ($isRenewal) {
                // Send renewal email to user
                Mail::to($user->email)->send(new SubscriptionRenewed($user, $subscription, $package));
            } else {
                // Send activation email to user (new subscription)
                Mail::to($user->email)->send(new SubscriptionActivated($user, $subscription, $package));
            }
            
            // Send notification to admins (already sent when payment was created, but send again for approval)
            $this->sendAdminEmails(new PaymentReceived($payment, $user));
        } catch (\Throwable $e) {
            Log::error('Failed to send subscription approval emails', ['error' => $e->getMessage()]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.subscription_approved', 'Subscription approved successfully'));
    }

    /**
     * Approve a pending wallet payment
     */
    public function approveWalletPayment(Payment $payment)
    {
        if ($payment->type !== 'wallet' || $payment->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.payment_not_pending', 'Payment is not pending'));
        }

        // Update payment status
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Add amount to user wallet
        $user = $payment->user;
        $user->wallet_balance = ($user->wallet_balance ?? 0) + $payment->amount;
        $user->save();

        // Create invoice
        \App\Models\Invoice::create([
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
            'type' => 'wallet',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'status' => 'paid',
            'paid_at' => now(),
            'details' => [
                'description' => 'Wallet top-up via bank transfer',
            ],
        ]);

        // Create transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'phone' => $user->phone,
            'type' => 'deposit',
            'description' => 'Wallet top-up via bank transfer',
            'status' => 'paid',
        ]);

        // Send notification to admins (already sent when payment was created, but send again for approval)
        try {
            $this->sendAdminEmails(new WalletTopupNotification($payment, $user));
        } catch (\Throwable $e) {
            Log::error('Failed to send wallet topup approval email', ['error' => $e->getMessage()]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.wallet_payment_approved', 'Wallet payment approved successfully'));
    }

    /**
     * Reject a pending subscription
     */
    public function reject(Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.subscription_not_pending', 'Subscription is not pending'));
        }

        $payment = $subscription->payment;

        if (!$payment || $payment->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.payment_not_found', 'Payment not found'));
        }

        // Update payment status to failed
        $payment->update([
            'status' => 'failed',
        ]);

        // Update subscription status to expired/cancelled
        $subscription->update([
            'status' => 'expired',
        ]);

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.subscription_rejected', 'Subscription rejected successfully'));
    }

    /**
     * Reject a pending wallet payment
     */
    public function rejectWalletPayment(Payment $payment)
    {
        if ($payment->type !== 'wallet' || $payment->status !== 'pending') {
            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.payment_not_pending', 'Payment is not pending'));
        }

        // Update payment status to failed
        $payment->update([
            'status' => 'failed',
        ]);

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.wallet_payment_rejected', 'Wallet payment rejected successfully'));
    }

    /**
     * Activate subscription
     */
    protected function activateSubscription(Subscription $subscription, SubscriptionPackage $package, Payment $payment): void
    {
        // Deactivate all other active subscriptions
        Subscription::where('user_id', $subscription->user_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Activate subscription
        $subscription->status = 'active';
        $subscription->starts_at = now();
        $subscription->ends_at = $this->calculateSubscriptionEndDate($package);
        $subscription->payment_id = $payment->id;
        $subscription->save();
    }

    /**
     * Calculate subscription end date
     */
    protected function calculateSubscriptionEndDate(SubscriptionPackage $package)
    {
        return $package->interval === 'yearly' ? now()->addYear() : now()->addMonth();
    }

    /**
     * Process wallet deduction if used
     */
    protected function processWalletDeduction(Payment $payment, SubscriptionPackage $package): void
    {
        $metadata = $payment->metadata ?? [];
        if (!isset($metadata['use_wallet']) || !$metadata['use_wallet'] || !isset($metadata['wallet_amount_used'])) {
            return;
        }

        $walletAmountUsed = $metadata['wallet_amount_used'];
        if ($walletAmountUsed <= 0) {
            return;
        }

        $user = $payment->user;
        $user->wallet_balance = max(0, ($user->wallet_balance ?? 0) - $walletAmountUsed);
        $user->save();

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => -$walletAmountUsed,
            'payment_method' => 'wallet',
            'phone' => $user->phone,
            'type' => 'payment',
            'description' => 'Wallet deduction for package payment: ' . $package->name,
            'status' => 'paid',
        ]);
    }

    /**
     * Create invoice for package payment
     */
    protected function createPackageInvoice(Payment $payment, SubscriptionPackage $package): \App\Models\Invoice
    {
        $metadata = $payment->metadata ?? [];
        $packagePrice = $metadata['package_price'] ?? $payment->amount;
        $walletAmountUsed = $metadata['wallet_amount_used'] ?? 0;

        return \App\Models\Invoice::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
            'type' => 'package',
            'item_type' => SubscriptionPackage::class,
            'item_id' => $package->id,
            'amount' => $packagePrice,
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'status' => 'paid',
            'paid_at' => now(),
            'details' => [
                'package_name' => $package->name,
                'package_interval' => $package->interval,
                'package_price' => $packagePrice,
                'wallet_amount_used' => $walletAmountUsed,
                'amount_paid' => $payment->amount,
            ],
        ]);
    }

    /**
     * Create transaction record for package payment
     */
    protected function createPackageTransaction(Payment $payment, SubscriptionPackage $package): void
    {
        \App\Models\Transaction::create([
            'user_id' => $payment->user_id,
            'amount' => -$payment->amount,
            'payment_method' => $payment->payment_method,
            'phone' => $payment->user->phone,
            'type' => 'payment',
            'description' => 'Package subscription: ' . $package->name,
            'status' => 'paid',
        ]);
    }

    /**
     * Get user subscriptions
     */
    protected function getUserSubscriptions(User $user)
    {
        return $user->subscriptions()
            ->with(['package', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get current subscription for user
     */
    protected function getCurrentSubscription(User $user)
    {
        return $user->subscription()
            ->with('package')
            ->first();
    }

    /**
     * Get user statistics
     */
    private function getUserStats(User $user): array
    {
        return [
            'totalOrders' => $user->orders()->count(),
            'total_orders' => $user->orders()->count(),
            'totalProducts' => $user->products()->count(),
            'total_products' => $user->products()->count(),
            'totalPages' => $user->pages()->count(),
            'total_pages' => $user->pages()->count(),
            'totalSales' => $user->orders()->where('status', 'delivered')->sum('total_cents') ?? 0,
            'total_sales' => $user->orders()->where('status', 'delivered')->sum('total_cents') ?? 0,
        ];
    }

    /**
     * Send emails to all admin users
     */
    protected function sendAdminEmails($mailable): void
    {
        $adminEmails = User::where('role', 'admin')
            ->orWhere(function($query) {
                if (method_exists($query->getModel(), 'roles')) {
                    $query->whereHas('roles', function($q) {
                        $q->where('name', 'admin');
                    });
                }
            })
            ->pluck('email')
            ->filter()
            ->unique();

        foreach ($adminEmails as $email) {
            try {
                Mail::to($email)->send($mailable);
            } catch (\Throwable $e) {
                Log::error('Failed to send admin email', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
