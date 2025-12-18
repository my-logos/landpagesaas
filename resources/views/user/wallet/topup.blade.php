@extends('layouts.app')

@section('content')
<div class="wallet-topup-container" dir="{{ $dir }}">
  <div class="page-header">
    <h1 class="page-title">{{ $t('messages.add_balance') ?? 'Add Balance' }}</h1>
    <p class="page-subtitle">{{ $t('messages.current_balance') }}: <strong>{{ number_format($currentBalance, 2) }} {{ $t('messages.currency') }}</strong></p>
  </div>

  <div class="topup-form-wrapper">
    <form action="{{ route('payments.wallet') }}" method="POST" class="topup-form" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="amount">{{ $t('messages.amount') ?? 'Amount' }} ({{ $t('messages.currency') }})</label>
        <input
          type="number"
          id="amount"
          name="amount"
          class="form-control"
          min="1"
          step="0.01"
          placeholder="0.00"
          required>
        <small class="form-text">{{ $t('messages.minimum_amount') ?? 'Minimum amount: 1' }} {{ $t('messages.currency') }}</small>
      </div>

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

      <!-- Bank Transfer Information (shown only when bank_transfer is selected) -->
      <div id="bank_transfer_info_group">
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
          <div id="transfer_receipt_preview">
            <img id="transfer_receipt_preview_img" src="" alt="Receipt Preview">
          </div>
        </div>
      </div>

      <div class="form-actions visible">
        <a href="{{ route('user.transactions') }}" class="btn btn-secondary">{{ $t('messages.cancel') ?? 'Cancel' }}</a>
        <button type="submit" class="btn btn-primary btn-pay">
          <i class="fa-solid fa-credit-card"></i>
          {{ $t('messages.proceed_to_payment') ?? 'Proceed to Payment' }}
        </button>
      </div>
    </form>
  </div>
</div>

@push('head')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/payment-package.js') }}"></script>
@endpush
@endsection