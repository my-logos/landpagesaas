@extends('layouts.app')

@section('content')
<div class="transactions-page" dir="{{ $dir }}">
  <!-- Balance Banner -->
  <div class="balance-banner">
    <div class="balance-content">
      <div class="balance-label">{{ $t('messages.current_balance') }}</div>
      <div class="balance-amount">
        <i class="fa-solid fa-dollar-sign"></i>
        <span>{{ number_format($currentBalance ?? 0, 2) }} {{ $t('messages.currency') }}</span>
      </div>
      <a href="{{ route('user.wallet.topup') }}" class="btn-recharge">{{ $t('messages.recharge_balance_now') }}</a>
    </div>
  </div>

  <!-- Tabs -->
  <div class="transactions-tabs">
    <a href="{{ route('user.transactions') }}" class="tab-item active" data-tab="transactions">
      {{ $t('messages.transactions') }}
    </a>
    <a href="#" class="tab-item" data-tab="invoices">
      {{ $t('messages.invoices') }}
    </a>
  </div>

  <!-- Filter Section -->
  <div class="filter-section">
    <div class="filter-header">
      <i class="fa-solid fa-filter"></i>
      <span>{{ $t('messages.filter_transactions') }}</span>
    </div>
    <div class="filter-controls">
      <input type="text" class="filter-input" placeholder="{{ $t('messages.search') }}" />
      <button class="btn-show-filters">{{ $t('messages.show_filters') }}</button>
    </div>
  </div>

  <!-- Pending Wallet Payments (Bank Transfer) -->
  @if($pendingWalletPayments && $pendingWalletPayments->count() > 0)
  <div class="pending-payments-section">
    <h3>
      <i class="fa-solid fa-clock"></i> {{ $t('messages.pending_wallet_payments') ?? 'Pending Wallet Payments' }}
    </h3>
    <div class="pending-payments-list">
      @foreach($pendingWalletPayments as $payment)
      <div class="pending-payment-item">
        <div>
          <div class="pending-payment-amount">{{ number_format($payment->amount, 2) }} {{ $t('messages.currency') }}</div>
          <div class="pending-payment-details">{{ $t('messages.bank_transfer') ?? 'Bank Transfer' }} - {{ $payment->created_at->format('d M Y - H:i') }}</div>
          @if($payment->transfer_receipt)
          <div class="pending-payment-receipt-link">
            <a href="{{ asset('storage/payments/transfer-receipts/' . $payment->transfer_receipt) }}" target="_blank" class="btn btn-sm btn-info">
              <i class="fa-solid fa-image"></i> {{ $t('messages.view_receipt') ?? 'View Receipt' }}
            </a>
          </div>
          @endif
        </div>
        <div>
          <span class="pending-payment-badge">
            {{ $t('messages.pending') ?? 'Pending' }}
          </span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Transactions Table -->
  <div class="transactions-table-wrapper" data-tab-content="transactions">
    <table class="transactions-table">
      <thead>
        <tr>
          <th>{{ $t('messages.date') }}</th>
          <th>{{ $t('messages.amount') }}</th>
          <th>{{ $t('messages.payment_method') }}</th>
          <th>{{ $t('messages.phone_number') }}</th>
          <th>{{ $t('messages.transaction_type') }}</th>
          <th>{{ $t('messages.description') }}</th>
          <th>{{ $t('messages.status') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions ?? [] as $transaction)
        <tr>
          <td>{{ $transaction->created_at->format('d M Y - H:i') }}</td>
          <td>{{ number_format($transaction->amount, 2) }} {{ $t('messages.currency') }}</td>
          <td>{{ $transaction->payment_method ?? '-' }}</td>
          <td>{{ $transaction->phone ?? '-' }}</td>
          <td>{{ $transaction->type }}</td>
          <td>{{ $transaction->description }}</td>
          <td>
            <span class="status-badge status-{{ $transaction->status }}">
              <i class="fa-solid fa-check"></i>
              {{ $t('messages.' . $transaction->status) }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center">{{ $t('messages.no_transactions') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Invoices Table -->
  <div class="transactions-table-wrapper" data-tab-content="invoices">
    <table class="transactions-table">
      <thead>
        <tr>
          <th>{{ $t('messages.invoice_number') }}</th>
          <th>{{ $t('messages.date') }}</th>
          <th>{{ $t('messages.amount') }}</th>
          <th>{{ $t('messages.payment_method') }}</th>
          <th>{{ $t('messages.type') }}</th>
          <th>{{ $t('messages.status') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoices ?? [] as $invoice)
        <tr>
          <td>{{ $invoice->invoice_number }}</td>
          <td>{{ $invoice->created_at->format('d M Y - H:i') }}</td>
          <td>{{ number_format($invoice->amount, 2) }} {{ $t('messages.currency') }}</td>
          <td>{{ ucfirst($invoice->payment_method) }}</td>
          <td>{{ ucfirst($invoice->type) }}</td>
          <td>
            <span class="status-badge status-{{ $invoice->status }}">
              <i class="fa-solid fa-check"></i>
              {{ $t('messages.' . $invoice->status) }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center">{{ $t('messages.no_transactions') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-tabs.js') }}"></script>
@endpush
@endsection