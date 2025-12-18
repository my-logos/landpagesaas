<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\Payment;
use App\Mail\AccountActivated;
use App\Mail\EmailVerification;
use App\Mail\SubscriptionActivated;
use App\Mail\SubscriptionRenewed;
use App\Mail\SubscriptionExpiringSoon;
use App\Mail\NewSubscriptionNotification;
use App\Mail\PaymentReceived;
use App\Mail\WalletTopupNotification;
use Carbon\Carbon;

class TestEmailController extends Controller
{
    /**
     * Test all email types by sending them to a test email
     */
    public function testAllEmails(Request $request)
    {
        $testEmail = 'aboutsystem2@gmail.com';
        $results = [];

        // Get mail configuration for debugging
        $mailConfig = [
            'mailer' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_username' => config('mail.mailers.smtp.username') ? 'Set' : 'Not Set',
        ];

        try {
            // Get existing data or create minimal test data
            $testUser = User::first();
            if (!$testUser) {
                $testUser = new User();
                $testUser->name = 'Test User';
                $testUser->email = 'test@example.com';
                $testUser->phone = '+201234567890';
                $testUser->password = bcrypt('password');
                $testUser->is_active = true;
                $testUser->save();
            }

            $testPackage = SubscriptionPackage::first();
            if (!$testPackage) {
                $testPackage = SubscriptionPackage::create([
                    'name' => 'Test Package',
                    'slug' => 'test-package',
                    'price_cents' => 10000,
                    'is_free' => false,
                    'interval' => 'monthly',
                ]);
            }

            $testSubscription = Subscription::where('user_id', $testUser->id)->first();
            if (!$testSubscription) {
                $testSubscription = Subscription::create([
                    'user_id' => $testUser->id,
                    'package_id' => $testPackage->id,
                    'starts_at' => Carbon::now(),
                    'ends_at' => Carbon::now()->addMonth(),
                    'status' => 'active',
                ]);
            }

            $testPayment = Payment::where('type', 'package')->first();
            if (!$testPayment) {
                $testPayment = Payment::create([
                    'user_id' => $testUser->id,
                    'payment_method' => 'test',
                    'amount' => 10000,
                    'currency' => 'EGP',
                    'type' => 'package',
                    'payable_type' => SubscriptionPackage::class,
                    'payable_id' => $testPackage->id,
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);
            }

            // 0. Test EmailVerification email
            try {
                Mail::to($testEmail)->send(new EmailVerification($testUser));
                $results['EmailVerification'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['EmailVerification'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: EmailVerification', ['error' => $e->getMessage()]);
            }

            // 1. Test AccountActivated email
            try {
                Mail::to($testEmail)->send(new AccountActivated($testUser));
                $results['AccountActivated'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['AccountActivated'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: AccountActivated', ['error' => $e->getMessage()]);
            }

            // 2. Test SubscriptionActivated email
            try {
                Mail::to($testEmail)->send(new SubscriptionActivated($testUser, $testSubscription, $testPackage));
                $results['SubscriptionActivated'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['SubscriptionActivated'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: SubscriptionActivated', ['error' => $e->getMessage()]);
            }

            // 3. Test SubscriptionRenewed email
            try {
                Mail::to($testEmail)->send(new SubscriptionRenewed($testUser, $testSubscription, $testPackage));
                $results['SubscriptionRenewed'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['SubscriptionRenewed'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: SubscriptionRenewed', ['error' => $e->getMessage()]);
            }

            // 4. Test SubscriptionExpiringSoon email
            try {
                Mail::to($testEmail)->send(new SubscriptionExpiringSoon($testUser, $testSubscription, $testPackage, 7));
                $results['SubscriptionExpiringSoon'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['SubscriptionExpiringSoon'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: SubscriptionExpiringSoon', ['error' => $e->getMessage()]);
            }

            // 5. Test NewSubscriptionNotification email (admin)
            try {
                Mail::to($testEmail)->send(new NewSubscriptionNotification($testUser, $testSubscription, $testPackage));
                $results['NewSubscriptionNotification'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['NewSubscriptionNotification'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: NewSubscriptionNotification', ['error' => $e->getMessage()]);
            }

            // 6. Test PaymentReceived email (admin)
            try {
                Mail::to($testEmail)->send(new PaymentReceived($testPayment, $testUser));
                $results['PaymentReceived'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['PaymentReceived'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: PaymentReceived', ['error' => $e->getMessage()]);
            }

            // 7. Test WalletTopupNotification email (admin)
            try {
                $walletPayment = Payment::where('type', 'wallet')->first() ?? Payment::create([
                    'user_id' => $testUser->id,
                    'payment_method' => 'test',
                    'amount' => 5000,
                    'currency' => 'EGP',
                    'type' => 'wallet',
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);
                Mail::to($testEmail)->send(new WalletTopupNotification($walletPayment, $testUser));
                $results['WalletTopupNotification'] = 'Sent successfully';
            } catch (\Throwable $e) {
                $results['WalletTopupNotification'] = 'Failed: ' . $e->getMessage();
                Log::error('Test email failed: WalletTopupNotification', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'status' => 'success',
                'test_email' => $testEmail,
                'mail_config' => $mailConfig,
                'results' => $results,
                'message' => 'All test emails sent to ' . $testEmail,
                'warning' => $mailConfig['mailer'] === 'log' ? '⚠️ WARNING: Mail driver is set to "log". Emails are being saved to log file instead of being sent. Please set MAIL_MAILER=smtp in .env file with proper SMTP credentials.' : null
            ]);
        } catch (\Exception $e) {
            Log::error('Test emails failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'mail_config' => $mailConfig,
                'results' => $results,
                'warning' => $mailConfig['mailer'] === 'log' ? '⚠️ WARNING: Mail driver is set to "log". Emails are being saved to log file instead of being sent. Please set MAIL_MAILER=smtp in .env file with proper SMTP credentials.' : null
            ], 500);
        }
    }
}
