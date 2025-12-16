<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HandlesFileUploads;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use HasLocaleAndTranslation, HandlesFileUploads;

    /**
     * Show wallet top-up page
     */
    public function topUp()
    {
        $user = auth()->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $currentBalance = $user->wallet_balance ?? 0;

        // Get enabled payment gateways from database
        $paymentGateways = PaymentGateway::where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get bank transfer gateway details
        $bankTransferGateway = $paymentGateways->firstWhere('code', 'bank_transfer');

        return view('user.wallet.topup', compact('currentBalance', 'locale', 'dir', 't', 'paymentGateways', 'bankTransferGateway'));
    }
}
