@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
        <h1 class="page-title">{{ $t('messages.user_subscriptions') ?? 'User Subscriptions' }} - {{ $user->name }}</h1>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_orders') }}</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_products') }}</div>
                <div class="stat-value">{{ $totalProducts }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <i class="fa-solid fa-link"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_pages') }}</div>
                <div class="stat-value">{{ $totalPages }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_sales') }}</div>
                <div class="stat-value">{{ number_format($totalSales / 100, 2) }} {{ $t('messages.currency') }}</div>
            </div>
        </div>
    </div>

    <!-- Current Subscription -->
    @if($currentSubscription)
    <div class="chart-card">
        <h3 class="chart-title">{{ $t('messages.current_subscription') ?? 'Current Subscription' }}</h3>
        <div class="plan-info-card">
            <div class="plan-info-item">
                <i class="fa-solid fa-gift"></i>
                <div>
                    <div class="info-label">{{ $t('messages.plan_name') }}</div>
                    <div class="info-value">{{ $currentSubscription->package->name ?? '-' }}</div>
                </div>
            </div>
            <div class="plan-info-item">
                <i class="fa-solid fa-calendar"></i>
                <div>
                    <div class="info-label">{{ $t('messages.subscription_period') }}</div>
                    <div class="info-value">
                        {{ $currentSubscription->starts_at ? $currentSubscription->starts_at->format('d M Y') : '-' }} -
                        {{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>
            <div class="plan-info-item">
                <i class="fa-solid fa-info-circle"></i>
                <div>
                    <div class="info-label">{{ $t('messages.status') }}</div>
                    <div class="info-value">{{ $currentSubscription->status }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Pending Subscriptions with Bank Transfer -->
    @if($pendingSubscriptions && $pendingSubscriptions->count() > 0)
    <div class="chart-card">
        <h3 class="chart-title">{{ $t('messages.pending_subscriptions') ?? 'Pending Subscriptions' }}</h3>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.plan_name') }}</th>
                        <th>{{ $t('messages.payment_method') }}</th>
                        <th>{{ $t('messages.amount') }}</th>
                        <th>{{ $t('messages.transfer_receipt') }}</th>
                        <th>{{ $t('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingSubscriptions as $subscription)
                    @if($subscription->payment && $subscription->payment->payment_method === 'bank_transfer')
                    <tr>
                        <td>{{ $subscription->package->name ?? '-' }}</td>
                        <td>{{ $t('messages.bank_transfer') ?? 'Bank Transfer' }}</td>
                        <td>{{ number_format($subscription->payment->amount, 2) }} {{ $subscription->payment->currency }}</td>
                        <td>
                            @if($subscription->payment->transfer_receipt)
                            <a href="{{ asset('storage/payments/transfer-receipts/' . $subscription->payment->transfer_receipt) }}" target="_blank" class="btn btn-sm btn-info receipt-view-btn">
                                <i class="fa-solid fa-image"></i> {{ $t('messages.view_receipt') ?? 'View Receipt' }}
                            </a>
                            <img src="{{ asset('storage/payments/transfer-receipts/' . $subscription->payment->transfer_receipt) }}" alt="Receipt" class="transfer-receipt-image">
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
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
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Pending Wallet Payments with Bank Transfer -->
    @if($pendingWalletPayments && $pendingWalletPayments->count() > 0)
    <div class="chart-card">
        <h3 class="chart-title">{{ $t('messages.pending_wallet_payments') ?? 'Pending Wallet Payments' }}</h3>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
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

    <!-- All Subscriptions -->
    <div class="chart-card">
        <h3 class="chart-title">{{ $t('messages.all_subscriptions') ?? 'All Subscriptions' }}</h3>
        <div class="transactions-table-wrapper">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.plan_name') }}</th>
                        <th>{{ $t('messages.status') }}</th>
                        <th>{{ $t('messages.subscription_period') }}</th>
                        <th>{{ $t('messages.expires_on') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">{{ $t('messages.no_subscriptions') ?? 'No subscriptions found' }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-subscriptions.js') }}"></script>
@endpush
@endsection