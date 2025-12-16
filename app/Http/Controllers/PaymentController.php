<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nafezly\Payments\Classes\PaymobPayment;
use Nafezly\Payments\Classes\KashierPayment;
use Nafezly\Payments\Classes\FawryPayment;
use Nafezly\Payments\Classes\HyperpayPayment;
use Nafezly\Payments\Classes\PaypalPayment;
use Nafezly\Payments\Classes\ThawaniPayment;
use Nafezly\Payments\Classes\TapPayment;
use Nafezly\Payments\Classes\OpayPayment;
use Nafezly\Payments\Classes\PaymobWalletPayment;
use Nafezly\Payments\Classes\PaytabsPayment;
use Nafezly\Payments\Classes\BinancePayment;
use Nafezly\Payments\Classes\NowPaymentsPayment;
use Nafezly\Payments\Classes\PayeerPayment;
use Nafezly\Payments\Classes\PerfectMoneyPayment;
use Nafezly\Payments\Classes\TelrPayment;
use Nafezly\Payments\Classes\ClickPayPayment;
use Nafezly\Payments\Classes\CoinPaymentsPayment;
use Nafezly\Payments\Classes\BigPayPayment;
use Nafezly\Payments\Classes\EnotPayment;
use Nafezly\Payments\Classes\PaycecPayment;
use Nafezly\Payments\Classes\PayPalCreditPayment;
use Nafezly\Payments\Classes\PayrexxPayment;
use Nafezly\Payments\Classes\CryptomusPayment;
use Nafezly\Payments\Classes\PrimePayment;
use Nafezly\Payments\Classes\PaylinkPayment;
use Nafezly\Payments\Classes\PaySkyPayment;
use Nafezly\Payments\Classes\YallaPayPayment;
use Nafezly\Payments\Classes\OneLatPayment;
use Nafezly\Payments\Classes\PayopPayment;
use Nafezly\Payments\Classes\MamoPayment;
use Nafezly\Payments\Classes\StripePayment;
use Nafezly\Payments\Classes\WisePayment;
use Nafezly\Payments\Classes\ChangellyPayment;
use App\Helpers\TranslationHelper;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HandlesFileUploads;
use App\Services\PaymentGatewayService;
use App\Models\PaymentGateway;

/**
 * Payment Controller
 * Handles all payment-related operations including package subscriptions and wallet top-ups
 */

class PaymentController extends Controller
{
    use HasLocaleAndTranslation, HandlesFileUploads;

    /**
     * Show payment page for package
     */
    public function showPackagePayment(SubscriptionPackage $package)
    {
        $user = $this->requireAuthenticatedUser();
        if (!$user) {
            return $this->redirectToLogin();
        }

        if ($package->is_free) {
            return $this->handleFreePackageSubscription($user, $package);
        }

        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();
        $paymentData = $this->getPackagePaymentData($user, $package);

        // Check if wallet balance is sufficient
        $walletBalance = $user->wallet_balance ?? 0;
        $packagePrice = $package->price_cents;
        $walletSufficient = $walletBalance >= $packagePrice;

        // Only show payment gateways if wallet is not sufficient
        $paymentGateways = $walletSufficient ? collect([]) : $this->getEnabledPaymentGateways();

        // Get bank transfer gateway details (only if wallet is not sufficient)
        $bankTransferGateway = $walletSufficient ? null : $paymentGateways->firstWhere('code', 'bank_transfer');

        return view('payments.package', array_merge(
            compact('package', 'locale', 'dir', 't'),
            $paymentData,
            compact('paymentGateways', 'bankTransferGateway', 'walletSufficient')
        ));
    }

    /**
     * Handle free package subscription
     */
    protected function handleFreePackageSubscription($user, SubscriptionPackage $package)
    {
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribeUserToPackage($user, $package->id);
        return redirect()->route('user.dashboard')
            ->with('success', $this->getTranslatedMessage('messages.subscription_created', 'Subscription created successfully'));
    }

    /**
     * Get package payment data
     */
    protected function getPackagePaymentData($user, SubscriptionPackage $package): array
    {
        $walletBalance = $user->wallet_balance ?? 0;
        $packagePrice = $package->price_cents;
        $remainingAmount = max(0, $packagePrice - $walletBalance);

        return compact('walletBalance', 'packagePrice', 'remainingAmount');
    }

    /**
     * Get enabled payment gateways
     */
    protected function getEnabledPaymentGateways()
    {
        return PaymentGateway::where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Initialize payment for package subscription
     */
    public function payPackage(Request $request, SubscriptionPackage $package)
    {
        $user = $this->requireAuthenticatedUser();
        if (!$user) {
            return $this->redirectToLogin();
        }

        // Check if package is free
        if ($package->is_free) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->subscribeUserToPackage($user, $package->id);
            return redirect()->route('user.dashboard')->with('success', TranslationHelper::get('messages.subscription_created'));
        }

        $packagePrice = $package->price_cents;
        $walletBalance = $user->wallet_balance ?? 0;

        // Check if wallet balance is sufficient
        if ($walletBalance >= $packagePrice) {
            // Wallet payment - process directly
            $paymentMethod = 'wallet';
            $useWallet = true;
            $walletAmountUsed = $packagePrice;
            $amountToPay = 0;
        } else {
            // Regular payment gateway
            $paymentMethod = $request->input('payment_method', 'paymob');
            $useWallet = $walletBalance > 0;
            // Calculate wallet usage and amount to pay
            [$walletAmountUsed, $amountToPay] = $this->calculateWalletUsage($user, $packagePrice, $useWallet);
        }

        // Get currency from settings
        $currency = $this->getCurrencyCode();

        // Handle wallet payment (when balance is sufficient)
        if ($paymentMethod === 'wallet' && $walletBalance >= $packagePrice) {
            // Create subscription first (before payment)
            $subscriptionService = app(SubscriptionService::class);
            $subscription = $subscriptionService->subscribeUserToPackage($user, $package->id);

            // Create payment record
            $payment = $this->createPayment([
                'user_id' => $user->id,
                'payment_method' => 'wallet',
                'amount' => 0, // No additional payment needed
                'currency' => $currency,
                'type' => 'package',
                'payable_type' => SubscriptionPackage::class,
                'payable_id' => $package->id,
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => [
                    'package_price' => $packagePrice,
                    'wallet_amount_used' => $packagePrice,
                    'use_wallet' => true,
                ],
            ]);

            // Deduct from wallet
            $this->deductWalletBalance($user, $packagePrice);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => -$packagePrice,
                'payment_method' => 'wallet',
                'type' => 'debit',
                'description' => 'Wallet deduction for package payment: ' . $package->name,
                'payment_id' => $payment->id,
            ]);

            // Activate subscription and link payment
            $this->activateSubscription($subscription, $package, $payment);
            $this->activateUserAccount($user);

            // Create invoice
            $invoice = $this->createPackageInvoice($payment, $package);
            $this->createPackageTransaction($payment, $package);

            ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

            return view('payments.success', compact('invoice', 'locale', 'dir', 't'));
        }

        // Handle bank transfer payment
        if (strtolower($paymentMethod) === 'bank_transfer') {
            // Validate transfer receipt image
            $request->validate([
                'transfer_receipt' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Upload transfer receipt
            $receiptFileName = $this->uploadImage($request, 'transfer_receipt', 'payments/transfer-receipts');

            if (!$receiptFileName) {
                return redirect()->route('payments.package.show', $package)
                    ->with('error', TranslationHelper::get('messages.transfer_receipt_required', 'Transfer receipt is required'));
            }

            // Create payment record with transfer receipt
            $payment = $this->createPayment([
                'user_id' => $user->id,
                'payment_method' => $paymentMethod,
                'amount' => $amountToPay,
                'currency' => $currency,
                'type' => 'package',
                'payable_type' => SubscriptionPackage::class,
                'payable_id' => $package->id,
                'status' => 'pending', // Keep as pending until admin approves
                'transfer_receipt' => $receiptFileName,
                'metadata' => [
                    'package_price' => $packagePrice,
                    'wallet_amount_used' => $walletAmountUsed,
                    'use_wallet' => $useWallet,
                ],
            ]);

            // Create pending subscription
            $subscriptionService = app(SubscriptionService::class);
            $subscription = $subscriptionService->subscribeUserToPackage($user, $package->id);
            $subscription->payment_id = $payment->id;
            $subscription->status = 'pending';
            $subscription->save();

            return redirect()->route('user.packages.index')
                ->with('success', TranslationHelper::get('messages.bank_transfer_pending', 'Your payment is pending approval. We will activate your subscription after verifying the bank transfer.'));
        }

        // Create payment record
        $payment = $this->createPayment([
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'amount' => $amountToPay,
            'currency' => $currency,
            'type' => 'package',
            'payable_type' => SubscriptionPackage::class,
            'payable_id' => $package->id,
            'metadata' => [
                'package_price' => $packagePrice,
                'wallet_amount_used' => $walletAmountUsed,
                'use_wallet' => $useWallet,
            ],
        ]);

        // If wallet covers full amount, process directly
        if ($amountToPay <= 0) {
            $this->deductWalletBalance($user, $walletAmountUsed);
            $payment->update(['status' => 'paid', 'paid_at' => now()]);
            return $this->handlePackagePayment($payment);
        }

        // Process payment through gateway
        return $this->processPaymentGateway($payment, $user, $amountToPay, $currency, 'user.packages.index');
    }

    /**
     * Initialize payment for wallet balance top-up
     */
    public function payWallet(Request $request)
    {
        $user = $this->requireAuthenticatedUser();
        if (!$user) {
            return $this->redirectToLogin();
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $amount = $request->input('amount');
        $paymentMethod = $request->input('payment_method');
        $currency = $this->getCurrencyCode();

        // Handle bank transfer payment
        if (strtolower($paymentMethod) === 'bank_transfer') {
            // Validate transfer receipt image
            $request->validate([
                'transfer_receipt' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Upload transfer receipt
            $receiptFileName = $this->uploadImage($request, 'transfer_receipt', 'payments/transfer-receipts');

            if (!$receiptFileName) {
                return redirect()->route('user.wallet.topup')
                    ->with('error', TranslationHelper::get('messages.transfer_receipt_required', 'Transfer receipt is required'));
            }

            // Create payment record with transfer receipt
            $payment = $this->createPayment([
                'user_id' => $user->id,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'currency' => $currency,
                'type' => 'wallet',
                'status' => 'pending', // Keep as pending until admin approves
                'transfer_receipt' => $receiptFileName,
            ]);

            return redirect()->route('user.transactions')
                ->with('success', TranslationHelper::get('messages.bank_transfer_pending', 'Your payment is pending approval. We will add the balance to your wallet after verifying the bank transfer.'));
        }

        // Create payment record
        $payment = $this->createPayment([
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'currency' => $currency,
            'type' => 'wallet',
        ]);

        // Process payment through gateway
        return $this->processPaymentGateway($payment, $user, $amount, $currency, 'user.transactions');
    }

    /**
     * Verify payment callback from gateway-specific URL (e.g., /payments/verify/tap)
     */
    public function verifyGateway(Request $request, string $gateway)
    {
        try {
            $payment = $this->findPaymentByGateway($request, $gateway);

            if ($payment) {
                return $this->verify($request, $payment);
            }

            Log::warning('Payment verification: Payment not found for gateway', [
                'gateway' => $gateway,
                'request_data' => $request->all()
            ]);

            return redirect()->route('user.dashboard')
                ->with('error', $this->getTranslatedMessage('messages.payment_not_found', 'Payment not found'));
        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('user.dashboard')
                ->with('error', TranslationHelper::get('messages.payment_verification_failed'));
        }
    }

    /**
     * Find payment by gateway-specific parameters
     */
    protected function findPaymentByGateway(Request $request, string $gateway): ?Payment
    {
        $gateway = strtolower($gateway);
        $payment = $this->findPaymentByGatewayParams($request, $gateway);

        if (!$payment) {
            $payment = $this->findPaymentByMethod($gateway);
        }

        return $payment;
    }

    /**
     * Find payment by gateway-specific parameters
     */
    protected function findPaymentByGatewayParams(Request $request, string $gateway): ?Payment
    {
        return match ($gateway) {
            'tap' => $this->findPaymentByTapId($request),
            'paymob', 'paymob_wallet', 'paymobwallet' => $this->findPaymentByPaymobId($request),
            'kashier' => $this->findPaymentByKashierId($request),
            'fawry' => $this->findPaymentByFawryRef($request),
            default => $this->findPaymentByCommonParams($request),
        };
    }

    /**
     * Find payment by Tap ID
     */
    protected function findPaymentByTapId(Request $request): ?Payment
    {
        $tapId = $request->input('tap_id');
        if (!$tapId) {
            return null;
        }

        return Payment::where('payment_id', $tapId)
            ->orWhere('response_data', 'like', '%' . $tapId . '%')
            ->latest()
            ->first();
    }

    /**
     * Find payment by Paymob ID
     */
    protected function findPaymentByPaymobId(Request $request): ?Payment
    {
        $orderId = $request->input('order_id') ?? $request->input('transaction_id');
        if (!$orderId) {
            return null;
        }

        return Payment::where('payment_id', $orderId)
            ->orWhere('response_data', 'like', '%' . $orderId . '%')
            ->latest()
            ->first();
    }

    /**
     * Find payment by Kashier ID
     */
    protected function findPaymentByKashierId(Request $request): ?Payment
    {
        $paymentId = $request->input('payment_id') ?? $request->input('id');
        if (!$paymentId) {
            return null;
        }

        return Payment::where('payment_id', $paymentId)->latest()->first();
    }

    /**
     * Find payment by Fawry reference
     */
    protected function findPaymentByFawryRef(Request $request): ?Payment
    {
        $merchantRef = $request->input('merchantRefNumber') ?? $request->input('merchant_ref_number');
        if (!$merchantRef) {
            return null;
        }

        return Payment::where('payment_id', $merchantRef)->latest()->first();
    }

    /**
     * Find payment by common parameters
     */
    protected function findPaymentByCommonParams(Request $request): ?Payment
    {
        $paymentId = $request->input('payment_id')
            ?? $request->input('id')
            ?? $request->input('transaction_id')
            ?? $request->input('order_id');

        if (!$paymentId) {
            return null;
        }

        return Payment::where('payment_id', $paymentId)
            ->orWhere('response_data', 'like', '%' . $paymentId . '%')
            ->latest()
            ->first();
    }

    /**
     * Find payment by payment method
     */
    protected function findPaymentByMethod(string $gateway): ?Payment
    {
        $paymentMethod = $this->normalizePaymentMethod($gateway);

        return Payment::where('payment_method', $paymentMethod)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Normalize payment method name
     */
    protected function normalizePaymentMethod(string $gateway): string
    {
        return match ($gateway) {
            'paymobwallet' => 'paymob_wallet',
            default => $gateway,
        };
    }

    /**
     * Verify payment callback
     */
    public function verify(Request $request, Payment $payment = null)
    {
        try {
            $payment = $payment ?? $this->findPaymentFromRequest($request);

            if (!$payment) {
                Log::warning('Payment verification: Payment not found', [
                    'request_data' => $request->all(),
                    'user_id' => auth()->id()
                ]);
                return redirect()->route('user.dashboard')
                    ->with('error', $this->getTranslatedMessage('messages.payment_not_found', 'Payment not found'));
            }

            $verification = $this->verifyPaymentWithGateway($payment, $request);
            $isSuccess = $this->isPaymentSuccessful($verification);

            if ($isSuccess) {
                $this->markPaymentAsPaid($payment, $verification);
                return $this->handleSuccessfulPayment($payment);
            } else {
                $this->markPaymentAsFailed($payment, $verification);
                return $this->handleFailedPayment($payment, $verification);
            }
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('user.dashboard')
                ->with('error', TranslationHelper::get('messages.payment_verification_failed'));
        }
    }

    /**
     * Find payment from request parameters
     */
    protected function findPaymentFromRequest(Request $request): ?Payment
    {
        $paymentId = $this->extractPaymentIdFromRequest($request);

        if ($paymentId) {
            $payment = $this->findPaymentById($paymentId);
            if ($payment) {
                return $payment;
            }
        }

        return $this->findLatestPendingPayment();
    }

    /**
     * Extract payment ID from request
     */
    protected function extractPaymentIdFromRequest(Request $request): ?string
    {
        return $request->input('payment_id')
            ?? $request->input('id')
            ?? $request->input('transaction_id')
            ?? $request->input('order_id')
            ?? $request->input('tap_id');
    }

    /**
     * Find payment by ID
     */
    protected function findPaymentById(string $paymentId): ?Payment
    {
        return Payment::where('payment_id', $paymentId)
            ->orWhere('response_data', 'like', '%' . $paymentId . '%')
            ->latest()
            ->first();
    }

    /**
     * Find latest pending payment for authenticated user
     */
    protected function findLatestPendingPayment(): ?Payment
    {
        if (!auth()->check()) {
            return null;
        }

        return Payment::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Verify payment with gateway
     */
    protected function verifyPaymentWithGateway(Payment $payment, Request $request): array
    {
        $paymentGateway = $this->getPaymentGateway($payment->payment_method);
        return $paymentGateway->verify($request);
    }

    /**
     * Check if payment verification was successful
     */
    protected function isPaymentSuccessful(array $verification): bool
    {
        if ($this->hasSuccessFlag($verification)) {
            return true;
        }

        return $this->hasSuccessStatus($verification);
    }

    /**
     * Check if verification has success flag
     */
    protected function hasSuccessFlag(array $verification): bool
    {
        return $verification['success'] ?? false;
    }

    /**
     * Check if verification has success status
     */
    protected function hasSuccessStatus(array $verification): bool
    {
        $successStatuses = ['success', 'paid', 'completed', 'approved'];
        $status = strtolower($verification['status'] ?? '');
        $paymentStatus = strtolower($verification['payment_status'] ?? '');

        return in_array($status, $successStatuses) || in_array($paymentStatus, $successStatuses);
    }

    /**
     * Mark payment as paid
     */
    protected function markPaymentAsPaid(Payment $payment, array $verification): void
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'response_data' => json_encode($verification),
        ]);
    }

    /**
     * Mark payment as failed
     */
    protected function markPaymentAsFailed(Payment $payment, array $verification): void
    {
        $payment->update([
            'status' => 'failed',
            'response_data' => json_encode($verification),
        ]);

        Log::warning('Payment verification failed', [
            'payment_id' => $payment->id,
            'verification_result' => $verification
        ]);
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessfulPayment(Payment $payment)
    {
        if ($payment->type === 'package') {
            return $this->handlePackagePayment($payment);
        } elseif ($payment->type === 'wallet') {
            return $this->handleWalletPayment($payment);
        }

        return redirect()->route('user.dashboard')
            ->with('success', $this->getTranslatedMessage('messages.payment_successful', 'Payment successful'));
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment(Payment $payment, array $verification)
    {
        $errorMessage = $verification['message'] ?? TranslationHelper::get('messages.payment_failed');
        $route = $payment->type === 'package' ? 'user.packages.index' : 'user.wallet.topup';

        return redirect()->route($route)->with('error', $errorMessage);
    }

    /**
     * Handle successful package payment
     */
    protected function handlePackagePayment(Payment $payment)
    {
        $package = SubscriptionPackage::find($payment->payable_id);

        if (!$package) {
            return redirect()->route('user.packages.index')
                ->with('error', TranslationHelper::get('messages.package_not_found'));
        }

        $this->processWalletDeduction($payment, $package);
        $subscription = $this->getOrCreateSubscription($payment, $package);
        $this->activateSubscription($subscription, $package, $payment);
        $this->activateUserAccount($payment->user);
        $invoice = $this->createPackageInvoice($payment, $package);
        $this->createPackageTransaction($payment, $package);

        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        return view('payments.success', compact('invoice', 'locale', 'dir', 't'));
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

        // Check if wallet was already deducted (for wallet-only payments)
        if ($payment->payment_method === 'wallet' && $payment->status === 'paid') {
            // Wallet already deducted in payPackage method, just create transaction if not exists
            $existingTransaction = Transaction::where('payment_id', $payment->id)
                ->where('type', 'debit')
                ->first();

            if (!$existingTransaction) {
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => -$walletAmountUsed,
                    'payment_method' => 'wallet',
                    'type' => 'debit',
                    'description' => 'Wallet deduction for package payment: ' . $package->name,
                    'payment_id' => $payment->id,
                ]);
            }
        } else {
            // Deduct wallet balance
            $this->deductWalletBalance($user, $walletAmountUsed);

            Transaction::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'amount' => -$walletAmountUsed,
                'payment_method' => 'wallet',
                'phone' => $user->phone,
                'type' => 'payment',
                'description' => 'Wallet deduction for package payment: ' . $package->name,
                'status' => 'paid',
            ]);
        }
    }

    /**
     * Get or create subscription for payment
     */
    protected function getOrCreateSubscription(Payment $payment, SubscriptionPackage $package)
    {
        $subscription = \App\Models\Subscription::where('user_id', $payment->user_id)
            ->where('package_id', $package->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$subscription) {
            $subscription = \App\Models\Subscription::where('user_id', $payment->user_id)
                ->where('package_id', $package->id)
                ->latest()
                ->first();
        }

        if (!$subscription) {
            $subscriptionService = app(SubscriptionService::class);
            $subscription = $subscriptionService->subscribeUserToPackage($payment->user, $package->id);
        }

        return $subscription;
    }

    /**
     * Activate subscription and deactivate others
     */
    protected function activateSubscription($subscription, SubscriptionPackage $package, Payment $payment): void
    {
        // Deactivate all other active subscriptions
        \App\Models\Subscription::where('user_id', $payment->user_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Activate subscription
        $subscription->status = 'active';
        $subscription->starts_at = now();
        $subscription->ends_at = $this->calculateSubscriptionEndDate($package);
        $subscription->payment_id = $payment->id;
        $subscription->save();
        $subscription->refresh();
    }

    /**
     * Calculate subscription end date based on interval
     */
    protected function calculateSubscriptionEndDate(SubscriptionPackage $package)
    {
        return $package->interval === 'yearly' ? now()->addYear() : now()->addMonth();
    }

    /**
     * Activate user account if not already active
     */
    protected function activateUserAccount($user): void
    {
        if (!$user->is_active) {
            $user->is_active = true;
            $user->save();
        }
    }

    /**
     * Create invoice for package payment
     */
    protected function createPackageInvoice(Payment $payment, SubscriptionPackage $package): Invoice
    {
        $metadata = $payment->metadata ?? [];
        $packagePrice = $metadata['package_price'] ?? $payment->amount;
        $walletAmountUsed = $metadata['wallet_amount_used'] ?? 0;

        return Invoice::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
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
        Transaction::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'amount' => -$payment->amount,
            'payment_method' => $payment->payment_method,
            'phone' => $payment->user->phone,
            'type' => 'payment',
            'description' => 'Package subscription: ' . $package->name,
            'status' => 'paid',
        ]);
    }

    /**
     * Handle successful wallet payment
     */
    protected function handleWalletPayment(Payment $payment)
    {
        $this->addToWalletBalance($payment->user, $payment->amount);
        $invoice = $this->createWalletInvoice($payment);
        $this->createWalletTransaction($payment);

        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        return view('payments.success', compact('invoice', 'locale', 'dir', 't'));
    }

    /**
     * Add amount to wallet balance
     */
    protected function addToWalletBalance($user, float $amount): void
    {
        $user->wallet_balance = ($user->wallet_balance ?? 0) + $amount;
        $user->save();
    }

    /**
     * Create invoice for wallet payment
     */
    protected function createWalletInvoice(Payment $payment): Invoice
    {
        return Invoice::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'type' => 'wallet',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'status' => 'paid',
            'paid_at' => now(),
            'details' => [
                'description' => 'Wallet top-up',
            ],
        ]);
    }

    /**
     * Create transaction record for wallet payment
     */
    protected function createWalletTransaction(Payment $payment): void
    {
        Transaction::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'phone' => $payment->user->phone,
            'type' => 'deposit',
            'description' => 'Wallet top-up',
            'status' => 'paid',
        ]);
    }

    /**
     * Get payment gateway instance
     */
    protected function getPaymentGateway(string $method)
    {
        // Bank transfer doesn't need a gateway instance
        if (strtolower($method) === 'bank_transfer') {
            return null;
        }

        // Update config with credentials from database (if available)
        PaymentGatewayService::updateConfigForGateway(strtolower($method));

        return match (strtolower($method)) {
            'paymob' => new PaymobPayment(),
            'kashier' => new KashierPayment(),
            'fawry' => new FawryPayment(),
            'hyperpay' => new HyperpayPayment(),
            'paypal' => new PaypalPayment(),
            'thawani' => new ThawaniPayment(),
            'tap' => new TapPayment(),
            'opay' => new OpayPayment(),
            'paymob_wallet' => new PaymobWalletPayment(),
            'paymobwallet' => new PaymobWalletPayment(),
            'paytabs' => new PaytabsPayment(),
            'binance' => new BinancePayment(),
            'nowpayments' => new NowPaymentsPayment(),
            'payeer' => new PayeerPayment(),
            'perfectmoney' => new PerfectMoneyPayment(),
            'telr' => new TelrPayment(),
            'clickpay' => new ClickPayPayment(),
            'coinpayments' => new CoinPaymentsPayment(),
            'bigpay' => new BigPayPayment(),
            'enot' => new EnotPayment(),
            'paycec' => new PaycecPayment(),
            'paypal_credit' => new PayPalCreditPayment(),
            'paypalcredit' => new PayPalCreditPayment(),
            'payrexx' => new PayrexxPayment(),
            'cryptomus' => new CryptomusPayment(),
            'prime' => new PrimePayment(),
            'paylink' => new PaylinkPayment(),
            'paysky' => new PaySkyPayment(),
            'yallapay' => new YallaPayPayment(),
            'onelat' => new OneLatPayment(),
            'payop' => new PayopPayment(),
            'mamo' => new MamoPayment(),
            'stripe' => new StripePayment(),
            'wise' => new WisePayment(),
            'changelly' => new ChangellyPayment(),
            default => new PaymobPayment(),
        };
    }

    /**
     * Calculate wallet usage and amount to pay
     */
    protected function calculateWalletUsage($user, float $totalAmount, bool $useWallet): array
    {
        $walletBalance = $user->wallet_balance ?? 0;
        $walletAmountUsed = 0;
        $amountToPay = $totalAmount;

        if ($useWallet && $walletBalance > 0) {
            $walletAmountUsed = min($walletBalance, $totalAmount);
            $amountToPay = max(0, $totalAmount - $walletAmountUsed);
        }

        return [$walletAmountUsed, $amountToPay];
    }

    /**
     * Get currency code from settings
     */
    protected function getCurrencyCode(): string
    {
        $settingsService = app(\App\Services\SettingsService::class);
        return $settingsService->getCurrencyCode();
    }

    /**
     * Create payment record
     */
    protected function createPayment(array $data): Payment
    {
        return Payment::create(array_merge([
            'status' => 'pending',
        ], $data));
    }

    /**
     * Process payment through gateway
     */
    protected function processPaymentGateway(Payment $payment, $user, float $amount, string $currency, string $errorRoute)
    {
        try {
            $paymentGateway = $this->getPaymentGateway($payment->payment_method);

            // Bank transfer is handled separately
            if (!$paymentGateway) {
                return redirect()->route($errorRoute)
                    ->with('error', TranslationHelper::get('messages.payment_initiation_failed'));
            }

            $response = $paymentGateway
                ->setPaymentId($payment->id)
                ->setUserId($user->id)
                ->setUserFirstName($user->name)
                ->setUserLastName('')
                ->setUserEmail($user->email)
                ->setUserPhone($user->phone ?? '01000000000')
                ->setAmount($amount)
                ->setCurrency($currency)
                ->pay();

            // Update payment with gateway response
            if (isset($response['payment_id'])) {
                $payment->payment_id = $response['payment_id'];
                $payment->response_data = json_encode($response);
                $payment->save();
            }

            // Handle gateway response
            return $this->handleGatewayResponse($response, $errorRoute);
        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            $payment->update([
                'status' => 'failed',
                'response_data' => json_encode(['error' => $e->getMessage()])
            ]);

            return redirect()->route($errorRoute)
                ->with('error', TranslationHelper::get('messages.payment_initiation_failed'));
        }
    }

    /**
     * Handle gateway response
     */
    protected function handleGatewayResponse(array $response, string $errorRoute)
    {
        if (isset($response['redirect_url'])) {
            return redirect($response['redirect_url']);
        } elseif (isset($response['html'])) {
            return response($response['html']);
        } else {
            return redirect()->route($errorRoute)
                ->with('error', TranslationHelper::get('messages.payment_initiation_failed'));
        }
    }

    /**
     * Deduct wallet balance
     */
    protected function deductWalletBalance($user, float $amount): void
    {
        $user->wallet_balance = max(0, ($user->wallet_balance ?? 0) - $amount);
        $user->save();
    }

    /**
     * Require authenticated user or return null
     */
    protected function requireAuthenticatedUser()
    {
        return auth()->user() ?? request()->user();
    }

    /**
     * Redirect to login with error message
     */
    protected function redirectToLogin()
    {
        return redirect()->route('login')
            ->with('error', TranslationHelper::get('messages.please_login'));
    }
}
