<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Payment;

class TransactionController extends Controller
{
    use HasLocaleAndTranslation;

    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $transactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending wallet payments (bank transfer)
        $pendingWalletPayments = Payment::where('user_id', $user->id)
            ->where('type', 'wallet')
            ->where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get invoices for the user
        $invoices = Invoice::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $currentBalance = $user->wallet_balance ?? 0;

        // Initialize wallet if not exists
        if ($transactions->isEmpty() && $currentBalance == 0) {
            $this->initializeWallet($user);
            $transactions = $user->transactions()->orderBy('created_at', 'desc')->get();
            $currentBalance = 5.00;
        }

        return view('user.transactions', compact('transactions', 'invoices', 'currentBalance', 'pendingWalletPayments', 'locale', 'dir', 't'));
    }

    /**
     * Show wallet top-up form
     * @deprecated Use WalletController::topUp instead
     */
    public function topUpForm(Request $request)
    {
        return redirect()->route('user.wallet.topup');
    }

    /**
     * Initialize wallet for new user
     */
    private function initializeWallet($user): void
    {
        Transaction::create([
            'user_id' => $user->id,
            'amount' => 5.00,
            'payment_method' => 'System',
            'phone' => null,
            'type' => 'system',
            'description' => 'Wallet created',
            'status' => 'paid',
        ]);

        $user->update(['wallet_balance' => 5.00]);
    }
}
