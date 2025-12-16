@extends('layouts.app')

@section('content')
<div class="payment-page" dir="{{ $dir }}" data-wallet-balance="{{ $walletBalance ?? 0 }}" data-package-price="{{ $packagePrice ?? $package->price_cents }}" data-currency="{{ $t('messages.currency') }}">
  <div class="page-header">
    <h1 class="page-title">{{ $t('messages.payment_for_package') ?? 'Payment for Package' }}</h1>
  </div>

  <div class="payment-form-container">
    <div class="package-summary">
      <h3>{{ $package->name }}</h3>
      <div class="package-price">
        {{ number_format($packagePrice ?? $package->price_cents, 2) }} {{ $t('messages.currency') }}
      </div>
      @if($package->interval)
      <div class="package-interval">
        @if($package->interval === 'yearly')
        {{ $t('messages.per_yearly') ?? 'Yearly' }}
        @else
        {{ $t('messages.per_monthly') ?? 'Monthly' }}
        @endif
      </div>
      @endif
    </div>

    <!-- Wallet Balance Section - Only shown if wallet has balance but not sufficient -->
    @if(($walletBalance ?? 0) > 0 && !($walletSufficient ?? false))
    <div class="wallet-balance-section">
      <div class="wallet-info">
        <div class="wallet-balance-display">
          <i class="fa-solid fa-wallet"></i>
          <div>
            <span class="wallet-label">{{ $t('messages.wallet_balance') ?? 'Wallet Balance' }}:</span>
            <span class="wallet-amount">{{ number_format($walletBalance, 2) }} {{ $t('messages.currency') }}</span>
          </div>
        </div>
        <div class="wallet-auto-note">
          <i class="fa-solid fa-info-circle"></i>
          <span>{{ $t('messages.wallet_will_be_used_automatically') ?? 'Wallet balance will be used automatically' }}</span>
        </div>
      </div>
      <div class="payment-breakdown">
        <div class="breakdown-item">
          <span class="breakdown-label">{{ $t('messages.package_price') ?? 'Package Price' }}:</span>
          <span class="breakdown-value">{{ number_format($packagePrice, 2) }} {{ $t('messages.currency') }}</span>
        </div>
        <div class="breakdown-item">
          <span class="breakdown-label">{{ $t('messages.wallet_deduction') ?? 'Wallet Deduction' }}:</span>
          <span class="breakdown-value text-success">-{{ number_format($walletBalance, 2) }} {{ $t('messages.currency') }}</span>
        </div>
        <div class="breakdown-item breakdown-total">
          <span class="breakdown-label">{{ $t('messages.amount_to_pay') ?? 'Amount to Pay via Gateway' }}:</span>
          <span class="breakdown-value">{{ number_format($remainingAmount, 2) }} {{ $t('messages.currency') }}</span>
        </div>
      </div>
    </div>
    @elseif(!($walletSufficient ?? false))
    <!-- Display full amount when no wallet balance -->
    <div class="payment-summary-amount">
      <div class="amount-to-pay">
        <span class="amount-label">{{ $t('messages.amount_to_pay') ?? 'Amount to Pay' }}:</span>
        <span class="amount-value">{{ number_format($packagePrice, 2) }} {{ $t('messages.currency') }}</span>
      </div>
    </div>
    @endif

    <form action="{{ route('payments.package', $package) }}" method="POST" class="payment-form" enctype="multipart/form-data">
      @csrf

      <!-- Hidden input to track wallet usage - always use wallet if balance exists -->
      <input type="hidden" name="use_wallet" id="use_wallet_hidden" value="{{ ($walletBalance ?? 0) > 0 ? '1' : '0' }}">

      @if($walletSufficient ?? false)
      <!-- Wallet Payment Only - When wallet balance is sufficient -->
      <div class="wallet-payment-only">
        <div class="wallet-payment-info">
          <i class="fa-solid fa-wallet"></i>
          <div>
            <h4>{{ $t('messages.pay_from_wallet') ?? 'Pay from Wallet' }}</h4>
            <p>{{ $t('messages.wallet_balance_sufficient') ?? 'Your wallet balance is sufficient to pay for this package.' }}</p>
          </div>
        </div>
        <div class="wallet-payment-details">
          <div class="payment-detail-item">
            <span class="detail-label">{{ $t('messages.package_price') ?? 'Package Price' }}:</span>
            <span class="detail-value">{{ number_format($packagePrice, 2) }} {{ $t('messages.currency') ?? 'EGP' }}</span>
          </div>
          <div class="payment-detail-item">
            <span class="detail-label">{{ $t('messages.wallet_balance') ?? 'Wallet Balance' }}:</span>
            <span class="detail-value">{{ number_format($walletBalance, 2) }} {{ $t('messages.currency') }}</span>
          </div>
          <div class="payment-detail-item payment-detail-total">
            <span class="detail-label">{{ $t('messages.remaining_balance') ?? 'Remaining Balance' }}:</span>
            <span class="detail-value">{{ number_format($walletBalance - $packagePrice, 2) }} {{ $t('messages.currency') }}</span>
          </div>
        </div>
        <input type="hidden" name="payment_method" value="wallet">
      </div>
      @else
      <!-- Payment Gateways - When wallet is not sufficient -->
      <div class="form-group">
        <label for="payment_method">{{ $t('messages.payment_method') ?? 'Payment Method' }}</label>
        <select id="payment_method" name="payment_method" class="form-control" required>
          <option value="">{{ $t('messages.select_payment_method') ?? 'Select Payment Method' }}</option>
          @forelse($paymentGateways ?? [] as $gateway)
          <option value="{{ $gateway->code }}">{{ $gateway->name }}</option>
          @empty
          <option value="" disabled>{{ $t('messages.no_payment_gateways_available') ?? 'No payment gateways available' }}</option>
          @endforelse
        </select>
      </div>
      @endif

      <!-- Bank Transfer Information (shown only when bank_transfer is selected and wallet is not sufficient) -->
      @if(!($walletSufficient ?? false))
      <div id="bank_transfer_info_group" style="display: none;">
        @if($bankTransferGateway && $bankTransferGateway->description)
        <div class="form-group bank-transfer-info">
          <label>{{ $t('messages.bank_transfer_details') ?? 'Bank Transfer Details' }}</label>
          <div class="bank-transfer-details">
            {!! nl2br(e($bankTransferGateway->description)) !!}
          </div>
        </div>
        @endif

        <!-- Bank Transfer Receipt Upload -->
        <div class="form-group" id="transfer_receipt_group">
          <label for="transfer_receipt">{{ $t('messages.transfer_receipt') ?? 'Transfer Receipt' }} <span class="text-danger">*</span></label>
          <input type="file" id="transfer_receipt" name="transfer_receipt" class="form-control" accept="image/*" required>
          <small class="form-text text-muted">{{ $t('messages.upload_transfer_receipt') ?? 'Please upload a photo of your bank transfer receipt' }}</small>
          <div id="transfer_receipt_preview" style="margin-top: 10px; display: none;">
            <img id="transfer_receipt_preview_img" src="" alt="Receipt Preview" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
          </div>
        </div>
      </div>
      @endif

      <div class="form-actions visible">
        <a href="{{ route('user.packages.index') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
        <button type="submit" class="btn btn-primary btn-pay">
          @if($walletSufficient ?? false)
          <i class="fa-solid fa-wallet"></i>
          {{ $t('messages.pay_from_wallet') ?? 'Pay from Wallet' }}
          @else
          <i class="fa-solid fa-credit-card"></i>
          {{ $t('messages.proceed_to_payment') ?? 'Proceed to Payment' }}
          @endif
        </button>
      </div>
    </form>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/payment-package.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/payment-package.js') }}"></script>
@endpush
@endsection