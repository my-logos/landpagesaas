<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Public landing page view (for visitors)
Route::get('/page/{id}', [App\Http\Controllers\PageViewController::class, 'show'])->name('page.show');

// Public order submission from landing pages (no auth required)
Route::post('/page/{id}/order', [App\Http\Controllers\PageViewController::class, 'storeOrder'])->name('page.order.store');

// Thank you page after order submission
Route::get('/order/{order}/thank-you', [App\Http\Controllers\PageViewController::class, 'thankYou'])->name('order.thank-you');

// Track order by order number
Route::get('/order/track/{order_number}', [App\Http\Controllers\PageViewController::class, 'trackOrder'])->name('order.track');

// Language switcher - redirect to referer or home
Route::get('/set-language/{lang}', function ($lang) {
    // Get available languages from database
    $availableLanguages = \App\Models\Language::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    // If no languages in DB, use default en/ar
    if ($availableLanguages->isEmpty()) {
        $availableLanguages = collect([
            (object)['code' => 'en'],
            (object)['code' => 'ar'],
        ]);
    }

    // Validate language - must be in available languages
    $validCodes = $availableLanguages->pluck('code')->toArray();
    if (in_array($lang, $validCodes)) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back()->with('language_changed', true);
})->name('set.language');

// Authentication / Registration multi-step
Route::get('register/step1', [RegistrationController::class, 'step1Form'])->name('register.step1');
Route::post('register/step1', [RegistrationController::class, 'step1Post'])->name('register.step1.post');
Route::get('register/step2', [RegistrationController::class, 'step2Form'])->name('register.step2');
Route::post('register/step2', [RegistrationController::class, 'step2Post'])->name('register.step2.post');
Route::get('register/step3', [RegistrationController::class, 'step3Form'])->name('register.step3');
Route::post('register/step3', [RegistrationController::class, 'step3Post'])->name('register.step3.post');

// Login / Logout
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password reset (request)
Route::get('password/reset', [LoginController::class, 'showForgotForm'])->name('password.request');
Route::post('password/email', [LoginController::class, 'sendResetLink'])->name('password.email');

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
// auth scaffolding (Breeze) will provide auth routes; protect dashboards with auth + role
use App\Http\Middleware\CheckRole;

Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
});

// User package management
Route::middleware(['auth', CheckRole::class . ':user'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('user.dashboard');
});

// Admin package management
use App\Http\Controllers\Admin\PackageController as AdminPackageController;

Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('packages', AdminPackageController::class)->except(['show']);
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::resource('users', App\Http\Controllers\Admin\UsersController::class);
    Route::post('users/{user}/toggle-active', [App\Http\Controllers\Admin\UsersController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/toggle-email-verified', [App\Http\Controllers\Admin\UsersController::class, 'toggleEmailVerified'])->name('users.toggle-email-verified');
    Route::get('subscriptions', [App\Http\Controllers\Admin\SubscriptionsController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{user}', [App\Http\Controllers\Admin\SubscriptionsController::class, 'show'])->name('subscriptions.show');
    Route::post('subscriptions/{subscription}/approve', [App\Http\Controllers\Admin\SubscriptionsController::class, 'approve'])->name('subscriptions.approve');
    Route::post('subscriptions/{subscription}/reject', [App\Http\Controllers\Admin\SubscriptionsController::class, 'reject'])->name('subscriptions.reject');
    Route::post('payments/{payment}/approve-wallet', [App\Http\Controllers\Admin\SubscriptionsController::class, 'approveWalletPayment'])->name('payments.approve-wallet');
    Route::post('payments/{payment}/reject-wallet', [App\Http\Controllers\Admin\SubscriptionsController::class, 'rejectWalletPayment'])->name('payments.reject-wallet');
    Route::resource('languages', App\Http\Controllers\Admin\LanguagesController::class);
    Route::get('translations', [App\Http\Controllers\Admin\TranslationsController::class, 'index'])->name('translations.index');
    Route::post('translations', [App\Http\Controllers\Admin\TranslationsController::class, 'store'])->name('translations.store');
    Route::put('translations', [App\Http\Controllers\Admin\TranslationsController::class, 'update'])->name('translations.update');
    Route::get('additional-settings', [App\Http\Controllers\Admin\AdditionalSettingsController::class, 'index'])->name('additional-settings.index');
    Route::put('additional-settings', [App\Http\Controllers\Admin\AdditionalSettingsController::class, 'update'])->name('additional-settings.update');
    Route::get('facebook-conversion-api', [App\Http\Controllers\Admin\FacebookConversionAPIController::class, 'index'])->name('facebook-conversion-api.index');
    Route::put('facebook-conversion-api', [App\Http\Controllers\Admin\FacebookConversionAPIController::class, 'update'])->name('facebook-conversion-api.update');
    Route::get('landing-page-settings', [App\Http\Controllers\Admin\LandingPageSettingsController::class, 'index'])->name('landing-page-settings.index');
    Route::put('landing-page-settings', [App\Http\Controllers\Admin\LandingPageSettingsController::class, 'update'])->name('landing-page-settings.update');
    Route::resource('products', App\Http\Controllers\Admin\ProductsController::class)->only(['index', 'show']);
    Route::resource('pages', App\Http\Controllers\Admin\PagesController::class)->only(['index', 'show']);
    Route::get('payment-gateways/get-fields', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'getFieldsForGateway'])->name('payment-gateways.get-fields');
    Route::resource('payment-gateways', App\Http\Controllers\Admin\PaymentGatewayController::class);
    Route::resource('landing-page-templates', App\Http\Controllers\Admin\LandingPageTemplateController::class);
});

// Payment routes - must be before verify route for some gateways
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\WalletController;

Route::middleware('auth')->group(function () {
    // Show payment pages
    Route::get('/payments/package/{package}', [PaymentController::class, 'showPackagePayment'])->name('payments.package.show');
    Route::get('/wallet/topup', [WalletController::class, 'topUp'])->name('user.wallet.topup');

    // Process payments
    Route::post('/payments/package/{package}', [PaymentController::class, 'payPackage'])->name('payments.package');
    Route::post('/payments/wallet', [PaymentController::class, 'payWallet'])->name('payments.wallet');
});

// Verify route - may be called by payment gateways, so CSRF might be disabled
// Handle gateway-specific callbacks (e.g., /payments/verify/tap, /payments/verify/paymob)
Route::get('/payments/verify/{gateway}', [PaymentController::class, 'verifyGateway'])->name('verify-payment.gateway');
Route::post('/payments/verify/{gateway}', [PaymentController::class, 'verifyGateway'])->name('verify-payment.gateway.post');
// Generic verify route
Route::get('/payments/verify/{payment?}', [PaymentController::class, 'verify'])->name('verify-payment');
Route::post('/payments/verify/{payment?}', [PaymentController::class, 'verify'])->name('verify-payment.post');

// User package browse & subscribe
use App\Http\Controllers\User\PackageController as UserPackageController;

Route::middleware(['auth', CheckRole::class . ':user'])->prefix('user')->name('user.')->group(function () {
    Route::get('packages', [UserPackageController::class, 'index'])->name('packages.index');
    Route::post('packages/{package}/subscribe', [UserPackageController::class, 'subscribe'])->name('packages.subscribe');
    // Pages, Products, Orders resource routes for users
    Route::resource('pages', App\Http\Controllers\User\PagesController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);
    Route::post('pages/{page}/publish', [App\Http\Controllers\User\PagesController::class, 'publish'])->name('pages.publish');
    Route::get('pages/{page}/status', [App\Http\Controllers\User\PagesController::class, 'checkStatus'])->name('user.pages.status');
    Route::post('pages/{page}/tracking-ids', [App\Http\Controllers\User\PagesController::class, 'updateTrackingIds'])->name('pages.tracking-ids');
    Route::post('pages/{page}/settings', [App\Http\Controllers\User\PagesController::class, 'updateSettings'])->name('pages.settings');
    Route::post('pages/{page}/link', [App\Http\Controllers\User\PagesController::class, 'createLink'])->name('pages.link');
    Route::get('pages/{page}/variations', [App\Http\Controllers\User\PagesController::class, 'getVariations'])->name('pages.variations.get');
    Route::post('pages/{page}/variations', [App\Http\Controllers\User\PagesController::class, 'updateVariations'])->name('pages.variations.update');
    Route::post('pages/additional-sales', [App\Http\Controllers\User\PagesController::class, 'updateAdditionalSales'])->name('pages.additional-sales');
    Route::post('pages/test-api-key', [App\Http\Controllers\User\PagesController::class, 'testApiKey'])->name('pages.test-api-key');
    Route::resource('products', App\Http\Controllers\User\ProductsController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('orders', App\Http\Controllers\User\OrdersController::class)->only(['index', 'create', 'store']);
    Route::get('orders/statistics', [App\Http\Controllers\User\OrdersController::class, 'statistics'])->name('orders.statistics');
    Route::get('orders/abandoned', [App\Http\Controllers\User\OrdersController::class, 'abandoned'])->name('orders.abandoned');
    // AI Analytics
    Route::get('ai-analytics', [App\Http\Controllers\User\AIAnalyticsController::class, 'index'])->name('ai-analytics.index');
    Route::put('orders/{order}/shipping-status', [App\Http\Controllers\User\OrdersController::class, 'updateShippingStatus'])->name('orders.update-shipping-status');
    Route::post('orders/performance-settings', [App\Http\Controllers\User\OrdersController::class, 'updatePerformanceSettings'])->name('orders.performance-settings');
    Route::get('orders/export', [App\Http\Controllers\User\OrdersController::class, 'export'])->name('orders.export');
    // Profile and Transactions
    Route::get('profile', [App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\User\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('transactions', [App\Http\Controllers\User\TransactionController::class, 'index'])->name('transactions');
    // Facebook Conversion API
    Route::get('facebook-conversion-api', [App\Http\Controllers\User\FacebookConversionAPIController::class, 'index'])->name('facebook-conversion-api.index');
    Route::post('facebook-conversion-api', [App\Http\Controllers\User\FacebookConversionAPIController::class, 'update'])->name('facebook-conversion-api.update');
    // Webhooks
    Route::get('webhooks', [App\Http\Controllers\User\WebhookController::class, 'index'])->name('webhooks.index');
    Route::get('webhooks/{webhook}', [App\Http\Controllers\User\WebhookController::class, 'show'])->name('webhooks.show');
    Route::post('webhooks', [App\Http\Controllers\User\WebhookController::class, 'store'])->name('webhooks.store');
    Route::put('webhooks/{webhook}', [App\Http\Controllers\User\WebhookController::class, 'update'])->name('webhooks.update');
    Route::delete('webhooks/{webhook}', [App\Http\Controllers\User\WebhookController::class, 'destroy'])->name('webhooks.destroy');
    Route::post('webhooks/{webhook}/toggle', [App\Http\Controllers\User\WebhookController::class, 'toggle'])->name('webhooks.toggle');
    Route::post('webhooks/{webhook}/test', [App\Http\Controllers\User\WebhookController::class, 'test'])->name('webhooks.test');
});

// Upgrade required page
Route::middleware('auth')->get('/upgrade-required/{feature?}', [App\Http\Controllers\UpgradeRequiredController::class, 'show'])->name('upgrade.required');
