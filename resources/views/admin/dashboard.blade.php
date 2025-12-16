@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.dashboard') }}</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_users') ?? 'Total Users' }}</div>
                <div class="stat-value">{{ $totalUsers }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <i class="fa-solid fa-link"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_pages') ?? 'Total Pages' }}</div>
                <div class="stat-value">{{ $totalPages }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-red">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_products') ?? 'Total Products' }}</div>
                <div class="stat-value">{{ $totalProducts }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_packages') ?? 'Total Packages' }}</div>
                <div class="stat-value">{{ $totalPackages }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.free_subscriptions') ?? 'Free Subscriptions' }}</div>
                <div class="stat-value">{{ $freeSubscriptions }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.paid_subscriptions') ?? 'Paid Subscriptions' }}</div>
                <div class="stat-value">{{ $paidSubscriptions }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_paid') ?? 'Total Paid' }}</div>
                <div class="stat-value">{{ number_format($totalPaid / 100, 2) }} {{ $t('messages.currency') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_pending') ?? 'Total Pending' }}</div>
                <div class="stat-value">{{ number_format($totalPending / 100, 2) }} {{ $t('messages.currency') }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.subscriptions_distribution') ?? 'Subscriptions Distribution' }}</h3>
            <div class="chart-placeholder">
                <canvas id="subscriptionsChart" data-chart-data="{{ json_encode($chartDataSubscriptions) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.payments_distribution') ?? 'Payments Distribution' }}</h3>
            <div class="chart-placeholder">
                <canvas id="paymentsChart" data-chart-data="{{ json_encode($chartDataPayments) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.resources_overview') ?? 'Resources Overview' }}</h3>
            <div class="chart-placeholder">
                <canvas id="resourcesChart" data-chart-data="{{ json_encode($chartDataResources) }}"></canvas>
            </div>
        </div>
    </div>
</div>

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endpush
@endsection