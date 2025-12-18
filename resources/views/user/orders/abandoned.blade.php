@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.abandoned_orders_management') ?? 'Abandoned Orders Management' }}</h1>
        </div>
    </div>

    <!-- Statistics and Actions -->
    <div class="abandoned-orders-stats">
        <div class="total-orders-count">
            {{ $t('messages.total_orders') ?? 'Total Orders' }}: <strong>{{ $totalCount }}</strong>
        </div>
        <a href="{{ route('user.orders.abandoned') }}" class="btn-refresh">
            <i class="fa-solid fa-rotate"></i>
            {{ $t('messages.refresh') ?? 'Refresh' }}
        </a>
    </div>

    <!-- Informational Banner -->
    <div class="info-banner">
        <div class="info-banner-icon">
            <i class="fa-solid fa-info-circle"></i>
        </div>
        <div class="info-banner-content">
            {{ $t('messages.abandoned_orders_info') ?? 'On this page, you will find duplicate orders (orders created two or more times for the same customer) and incomplete orders (when the user starts filling out the order form but does not complete it or does not click the submit button).' }}
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-bar-left">
            <button class="btn-show-filter" type="button">
                {{ $t('messages.show') ?? 'Show' }}
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
        <div class="filter-bar-center">
            <form method="GET" action="{{ route('user.orders.abandoned') }}" class="search-form">
                <input type="hidden" name="filter_type" value="{{ $filterType }}">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="{{ $t('messages.quick_search') ?? 'Quick Search...' }}"
                        value="{{ $search }}">
                </div>
            </form>
        </div>
        <div class="filter-bar-right">
            <button type="button" class="btn-filter-abandoned" data-submit-form=".search-form">
                <i class="fa-solid fa-filter"></i>
                {{ $t('messages.filter_abandoned_orders') ?? 'Filter Abandoned Orders' }}
            </button>
        </div>
    </div>

    <!-- Orders List -->
    <div class="abandoned-orders-content">
        @if($abandonedOrders->count() > 0)
        <div class="abandoned-orders-table-wrapper">
            <table class="abandoned-orders-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.order_id') ?? 'Order ID' }}</th>
                        <th>{{ $t('messages.date') ?? 'Date' }}</th>
                        <th>{{ $t('messages.product') ?? 'Product' }}</th>
                        <th>{{ $t('messages.landing_page') ?? 'Landing Page' }}</th>
                        <th>{{ $t('messages.quantity') ?? 'Quantity' }}</th>
                        <th>{{ $t('messages.total') ?? 'Total' }}</th>
                        <th>{{ $t('messages.duplicate_count') ?? 'Duplicate Count' }}</th>
                        <th>{{ $t('messages.type') ?? 'Type' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($abandonedOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y - H:i') }}</td>
                        <td>{{ $order->product ? $order->product->name : '-' }}</td>
                        <td>{{ $order->landingPage ? $order->landingPage->title : '-' }}</td>
                        <td>{{ $order->quantity ?? 1 }}</td>
                        <td>{{ number_format($order->total_cents / 100, 2) }} {{ $order->currency ?? 'EGP' }}</td>
                        <td>
                            @if($order->is_duplicate ?? false)
                            <span class="duplicate-badge">{{ $order->duplicate_count ?? 1 }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($order->is_duplicate ?? false)
                            <span class="type-badge type-duplicate">
                                <i class="fa-solid fa-copy"></i>
                                {{ $t('messages.duplicate') ?? 'Duplicate' }}
                            </span>
                            @else
                            <span class="type-badge type-incomplete">
                                <i class="fa-solid fa-clock"></i>
                                {{ $t('messages.incomplete') ?? 'Incomplete' }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-abandoned-state">
            <div class="empty-icon-large">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h3 class="empty-title">{{ $t('messages.no_abandoned_orders') ?? 'No abandoned orders found' }}</h3>
            <p class="empty-description">
                {{ $t('messages.no_abandoned_orders_description') ?? 'No abandoned orders were found. This is a good thing!' }}
            </p>
        </div>
        @endif
    </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/user-abandoned-orders.js') }}"></script>
@endpush
@endsection