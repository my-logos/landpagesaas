@extends('layouts.app')

@section('content')
<div class="payment-success-page" dir="{{ $dir }}">
  <div class="success-container">
    <div class="success-icon">
      <i class="fa-solid fa-check-circle"></i>
    </div>
    <h1 class="success-title">{{ $t('messages.payment_successful') ?? 'Payment Successful' }}</h1>
    <p class="success-message">{{ $t('messages.payment_completed_successfully') ?? 'Your payment has been completed successfully' }}</p>

    @if(isset($invoice))
    <div class="invoice-details">
      <h3>{{ $t('messages.invoice_details') ?? 'Invoice Details' }}</h3>
      <div class="invoice-info">
        <div class="info-row">
          <span class="label">{{ $t('messages.invoice_number') ?? 'Invoice Number' }}:</span>
          <span class="value">{{ $invoice->invoice_number }}</span>
        </div>
        <div class="info-row">
          <span class="label">{{ $t('messages.amount') ?? 'Amount' }}:</span>
          <span class="value">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        <div class="info-row">
          <span class="label">{{ $t('messages.payment_method') ?? 'Payment Method' }}:</span>
          <span class="value">{{ ucfirst($invoice->payment_method) }}</span>
        </div>
        <div class="info-row">
          <span class="label">{{ $t('messages.date') ?? 'Date' }}:</span>
          <span class="value">{{ $invoice->paid_at->format('Y-m-d H:i') }}</span>
        </div>
      </div>
    </div>
    @endif

    <div class="success-actions">
      @if(isset($invoice) && $invoice->type === 'package')
      <a href="{{ route('user.packages.index') }}" class="btn btn-primary">{{ $t('messages.view_packages') ?? 'View Packages' }}</a>
      @elseif(isset($invoice) && $invoice->type === 'wallet')
      <a href="{{ route('user.transactions') }}" class="btn btn-primary">{{ $t('messages.view_transactions') ?? 'View Transactions' }}</a>
      @endif
      <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">{{ $t('messages.back_to_dashboard') ?? 'Back to Dashboard' }}</a>
    </div>
  </div>
</div>

@push('head')
<link rel="stylesheet" href="{{ asset('css/payment-success.css') }}">
@endpush
@endsection