<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends BaseAdminController
{
    /**
     * Available payment gateways with their credential fields
     */
    private const GATEWAY_FIELDS = [
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

    /**
     * Gateway display names
     */
    private const GATEWAY_NAMES = [
        'paymob' => 'Paymob',
        'kashier' => 'Kashier',
        'fawry' => 'Fawry',
        'hyperpay' => 'HyperPay',
        'paypal' => 'PayPal',
        'thawani' => 'Thawani',
        'tap' => 'Tap',
        'opay' => 'OPay',
        'paymob_wallet' => 'Paymob Wallet',
        'paytabs' => 'PayTabs',
        'binance' => 'Binance',
        'nowpayments' => 'NOWPayments',
        'payeer' => 'Payeer',
        'perfectmoney' => 'Perfect Money',
        'telr' => 'Telr',
        'clickpay' => 'ClickPay',
        'coinpayments' => 'CoinPayments',
        'bigpay' => 'BigPay',
        'enot' => 'Enot',
        'paycec' => 'PayCEC',
        'paypal_credit' => 'PayPal Credit',
        'payrexx' => 'Payrexx',
        'cryptomus' => 'Cryptomus',
        'prime' => 'Prime',
        'paylink' => 'PayLink',
        'paysky' => 'PaySky',
        'yallapay' => 'YallaPay',
        'onelat' => 'OneLat',
        'payop' => 'PayOp',
        'mamo' => 'Mamo',
        'stripe' => 'Stripe',
        'wise' => 'Wise',
        'changelly' => 'Changelly',
        'bank_transfer' => 'تحويل بنكي',
    ];

    public function index(Request $request)
    {
        $gateways = PaymentGateway::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.payment-gateways.index', $this->getViewData(compact('gateways')));
    }

    public function create(Request $request)
    {
        $existingCodes = PaymentGateway::pluck('code')->toArray();
        $availableGateways = array_diff_key(self::GATEWAY_NAMES, array_flip($existingCodes));

        return view('admin.payment-gateways.create', $this->getViewData(compact('availableGateways')));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:payment_gateways,code',
            'name' => 'required|string|max:255',
            'is_enabled' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'credentials' => 'nullable|array',
        ]);

        PaymentGateway::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'is_enabled' => $request->has('is_enabled'),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'credentials' => $validated['credentials'] ?? [],
        ]);

        return $this->redirectWithSuccess(
            'admin.payment-gateways.index',
            'messages.payment_gateway_created',
            'Payment gateway created successfully'
        );
    }

    public function edit(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $fields = self::GATEWAY_FIELDS[$gateway->code] ?? [];

        return view('admin.payment-gateways.edit', $this->getViewData(compact('gateway', 'fields')));
    }

    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_enabled' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'credentials' => 'nullable|array',
        ]);

        $gateway->update([
            'name' => $validated['name'],
            'is_enabled' => $request->has('is_enabled'),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $gateway->sort_order,
            'credentials' => $validated['credentials'] ?? $gateway->credentials,
        ]);

        return $this->redirectWithSuccess(
            'admin.payment-gateways.index',
            'messages.payment_gateway_updated',
            'Payment gateway updated successfully'
        );
    }

    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->delete();

        return $this->redirectWithSuccess(
            'admin.payment-gateways.index',
            'messages.payment_gateway_deleted',
            'Payment gateway deleted successfully'
        );
    }

    public function getFieldsForGateway(Request $request)
    {
        $code = strtolower(trim($request->input('code', '')));

        if (empty($code)) {
            return $this->jsonErrorResponse('Gateway code is required', 400);
        }

        if (!isset(self::GATEWAY_FIELDS[$code])) {
            return $this->jsonErrorResponse('Gateway code not found', 404);
        }

        return response()->json([
            'success' => true,
            'fields' => self::GATEWAY_FIELDS[$code]
        ]);
    }

    /**
     * Return JSON error response
     */
    protected function jsonErrorResponse(string $message, int $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'fields' => []
        ], $statusCode);
    }
}
