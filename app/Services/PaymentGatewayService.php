<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Config;

class PaymentGatewayService
{
    /**
     * Get credentials for a payment gateway from database
     * Falls back to env/config if not found in database
     */
    public static function getCredentials(string $code): array
    {
        $gateway = PaymentGateway::where('code', $code)
            ->where('is_enabled', true)
            ->first();

        if (!$gateway || empty($gateway->credentials)) {
            // Fallback to config/env
            return self::getCredentialsFromConfig($code);
        }

        return $gateway->credentials;
    }

    /**
     * Get credentials from config/env (fallback)
     */
    private static function getCredentialsFromConfig(string $code): array
    {
        $config = config('nafezly-payments', []);
        $credentials = [];

        // Map gateway codes to their config keys
        $fieldMappings = [
            'paymob' => ['PAYMOB_PUBLIC_KEY', 'PAYMOB_SECRET_KEY', 'PAYMOB_INTEGRATION_ID', 'PAYMOB_CURRENCY', 'PAYMOB_HMAC'],
            'kashier' => ['KASHIER_ACCOUNT_KEY', 'KASHIER_IFRAME_KEY', 'KASHIER_TOKEN', 'KASHIER_URL', 'KASHIER_MODE', 'KASHIER_CURRENCY', 'KASHIER_WEBHOOK_URL'],
            'fawry' => ['FAWRY_URL', 'FAWRY_SECRET', 'FAWRY_MERCHANT', 'FAWRY_DISPLAY_MODE', 'FAWRY_PAY_MODE'],
            'hyperpay' => ['HYPERPAY_BASE_URL', 'HYPERPAY_URL', 'HYPERPAY_TOKEN', 'HYPERPAY_CREDIT_ID', 'HYPERPAY_MADA_ID', 'HYPERPAY_APPLE_ID', 'HYPERPAY_CURRENCY'],
            'paypal' => ['PAYPAL_CLIENT_ID', 'PAYPAL_SECRET', 'PAYPAL_CURRENCY', 'PAYPAL_MODE'],
            'thawani' => ['THAWANI_API_KEY', 'THAWANI_URL', 'THAWANI_PUBLISHABLE_KEY'],
            'tap' => ['TAP_CURRENCY', 'TAP_SECRET_KEY', 'TAP_PUBLIC_KEY', 'TAP_LANG_KEY'],
            'opay' => ['OPAY_CURRENCY', 'OPAY_SECRET_KEY', 'OPAY_PUBLIC_KEY', 'OPAY_MERCHANT_ID', 'OPAY_COUNTRY_CODE', 'OPAY_BASE_URL'],
            'paymob_wallet' => ['PAYMOB_WALLET_INTEGRATION_ID'],
            'paytabs' => ['PAYTABS_PROFILE_ID', 'PAYTABS_SERVER_KEY', 'PAYTABS_BASE_URL', 'PAYTABS_CHECKOUT_LANG', 'PAYTABS_CURRENCY'],
            'binance' => ['BINANCE_API', 'BINANCE_SECRET'],
            'nowpayments' => ['NOWPAYMENTS_API_KEY'],
            'payeer' => ['PAYEER_MERCHANT_ID', 'PAYEER_API_KEY', 'PAYEER_ADDITIONAL_API_KEY'],
            'perfectmoney' => ['PERFECT_MONEY_ID', 'PERFECT_MONEY_PASSPHRASE'],
            'telr' => ['TELR_MERCHANT_ID', 'TELR_API_KEY', 'TELR_MODE'],
            'clickpay' => ['CLICKPAY_SERVER_KEY', 'CLICKPAY_PROFILE_ID'],
            'coinpayments' => ['COINPAYMENTS_PUBLIC_KEY', 'COINPAYMENTS_PRIVATE_KEY'],
            'bigpay' => ['BIGPAY_KEY', 'BIGPAY_SECRET', 'BIGPAY_MODE'],
            'enot' => ['ENOT_KEY', 'ENOT_SECRET', 'ENOT_SHOP_ID'],
            'paycec' => ['PAYCEC_MERCHANT_USERNAME', 'PAYCEC_MERCHANT_SECRET', 'PAYCEC_MODE'],
            'paypal_credit' => ['PAYPAL_CREDIT_CLIENT_ID', 'PAYPAL_CREDIT_SECRET', 'PAYPAL_CREDIT_MODE', 'PAYPAL_CREDIT_CURRENCY'],
            'payrexx' => ['PAYREXX_INSTANCE_NAME', 'PAYREXX_API_KEY'],
            'cryptomus' => ['CRYPTOMUS_MERCHANT_ID', 'CRYPTOMUS_API_KEY'],
            'prime' => ['PRIME_PROJECT_ID', 'PRIME_SECRET_WORD_1', 'PRIME_SECRET_WORD_2'],
            'paylink' => ['PAYLINK_API_KEY', 'PAYLINK_APP_ID', 'PAYLINK_MODE'],
            'paysky' => ['PAYSKY_MID', 'PAYSKY_TID', 'PAYSKY_SECRET', 'PAYSKY_MODE'],
            'yallapay' => ['YALLAPAY_PUBLIC_KEY', 'YALLAPAY_SECRET_KEY'],
            'onelat' => ['ONELAT_KEY', 'ONELAT_SECRET', 'ONELAT_API_BASE_URL', 'ONELAT_CHECKOUT_BASE_URL'],
            'payop' => ['PAYOP_PUBLIC_KEY', 'PAYOP_SECRET_KEY', 'PAYOP_JWT'],
            'mamo' => ['MAMOPAYMENT_BASE_URL', 'MAMOPAYMENT_API_KEY'],
            'stripe' => ['STRIPE_SECRET_KEY', 'STRIPE_PUBLIC_KEY', 'STRIPE_CURRENCY'],
            'wise' => ['WISE_API_KEY', 'WISE_PROFILE_ID'],
            'changelly' => ['CHANGELLY_API_KEY', 'CHANGELLY_SECRET_KEY'],
        ];

        $fields = $fieldMappings[strtolower($code)] ?? [];

        foreach ($fields as $field) {
            $credentials[$field] = $config[$field] ?? env($field);
        }

        return $credentials;
    }

    /**
     * Update config dynamically with database credentials
     */
    public static function updateConfigForGateway(string $code): void
    {
        $credentials = self::getCredentials($code);

        foreach ($credentials as $key => $value) {
            if ($value !== null) {
                Config::set("nafezly-payments.{$key}", $value);
            }
        }
    }
}
