@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
        <h1 class="page-title">{{ $t('messages.subscriptions') ?? 'Subscriptions' }}</h1>
    </div>

    <!-- Pending Wallet Payments -->
    @if($pendingWalletPayments && $pendingWalletPayments->count() > 0)
    <div class="chart-card pending-wallet-payments-card">
        <h3 class="chart-title">
            <i class="fa-solid fa-wallet"></i> {{ $t('messages.pending_wallet_payments') ?? 'Pending Wallet Payments' }}
        </h3>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.user') ?? 'User' }}</th>
                        <th>{{ $t('messages.date') }}</th>
                        <th>{{ $t('messages.amount') }}</th>
                        <th>{{ $t('messages.payment_method') }}</th>
                        <th>{{ $t('messages.transfer_receipt') }}</th>
                        <th>{{ $t('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingWalletPayments as $payment)
                    <tr>
                        <td>{{ $payment->user->name ?? '-' }}</td>
                        <td>{{ $payment->created_at->format('d M Y - H:i') }}</td>
                        <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td>{{ $t('messages.bank_transfer') ?? 'Bank Transfer' }}</td>
                        <td>
                            @if($payment->transfer_receipt)
                            <a href="{{ asset('storage/payments/transfer-receipts/' . $payment->transfer_receipt) }}" target="_blank" class="btn btn-sm btn-info receipt-view-btn">
                                <i class="fa-solid fa-image"></i> {{ $t('messages.view_receipt') ?? 'View Receipt' }}
                            </a>
                            <img src="{{ asset('storage/payments/transfer-receipts/' . $payment->transfer_receipt) }}" alt="Receipt" class="transfer-receipt-image">
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.payments.approve-wallet', $payment) }}" method="POST" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" data-confirm-approve-payment="{{ $t('messages.confirm_approve_payment') ?? 'Are you sure you want to approve this payment?' }}">
                                    <i class="fa-solid fa-check"></i> {{ $t('messages.approve') ?? 'Approve' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.payments.reject-wallet', $payment) }}" method="POST" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" data-confirm-reject-payment="{{ $t('messages.confirm_reject_payment') ?? 'Are you sure you want to reject this payment?' }}">
                                    <i class="fa-solid fa-times"></i> {{ $t('messages.reject') ?? 'Reject' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="chart-card">
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.user') ?? 'User' }}</th>
                        <th>{{ $t('messages.plan_name') }}</th>
                        <th>{{ $t('messages.status') }}</th>
                        <th>{{ $t('messages.subscription_period') }}</th>
                        <th>{{ $t('messages.expires_on') }}</th>
                        <th>{{ $t('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->user->name }}</td>
                        <td>{{ $subscription->package->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ $subscription->status }}
                            </span>
                        </td>
                        <td>
                            {{ $subscription->starts_at ? $subscription->starts_at->format('d M Y') : '-' }} -
                            {{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.subscriptions.show', $subscription->user) }}" class="btn btn-sm btn-primary">{{ $t('messages.view_details') ?? 'View Details' }}</a>
                            @if($subscription->status === 'pending' && $subscription->payment && $subscription->payment->payment_method === 'bank_transfer')
                            <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" data-confirm-approve-subscription="{{ $t('messages.confirm_approve_subscription') ?? 'Are you sure you want to approve this subscription?' }}">
                                    <i class="fa-solid fa-check"></i> {{ $t('messages.approve') ?? 'Approve' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" data-confirm-reject-subscription="{{ $t('messages.confirm_reject_subscription') ?? 'Are you sure you want to reject this subscription?' }}">
                                    <i class="fa-solid fa-times"></i> {{ $t('messages.reject') ?? 'Reject' }}
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ $t('messages.no_subscriptions') ?? 'No subscriptions found' }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrapper">
        {{ $subscriptions->links() }}
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-subscriptions.js') }}"></script>
@endpush
@endsection