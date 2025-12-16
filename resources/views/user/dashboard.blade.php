@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.dashboard') }}</h1>
        <div class="date-range-selector">
            <div class="date-input-group">
                <label>{{ $t('messages.date_from') }}:</label>
                <input type="date" class="date-input" placeholder="dd/mm/yyyy" />
                <i class="fa-solid fa-calendar date-icon"></i>
            </div>
            <div class="date-input-group">
                <label>{{ $t('messages.date_to') }}:</label>
                <input type="date" class="date-input" placeholder="dd/mm/yyyy" />
                <i class="fa-solid fa-calendar date-icon"></i>
            </div>
        </div>
    </div>

    <!-- Account Overview Section -->
    <div class="account-overview-section">
        <h2 class="section-title">{{ $t('messages.account_overview') ?? 'Account Overview' }}</h2>
        <div class="overview-grid">
            <!-- Wallet Balance Card -->
            <div class="overview-card wallet-card">
                <div class="overview-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="overview-content">
                    <div class="overview-label">{{ $t('messages.wallet_balance') }}</div>
                    <div class="overview-value">{{ number_format($walletBalance ?? 0, 2) }} {{ $t('messages.currency') }}</div>
                    <a href="{{ route('user.wallet.topup') }}" class="overview-link">{{ $t('messages.add_balance') ?? 'Add Balance' }}</a>
                </div>
            </div>

            <!-- Package Info Card -->
            <div class="overview-card package-card">
                <div class="overview-icon">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div class="overview-content">
                    <div class="overview-label">{{ $t('messages.your_current_package') ?? 'Current Package' }}</div>
                    <div class="overview-value">{{ ($currentPackage ? $currentPackage->name : $t('messages.free_plan')) }}</div>
                    @if($currentSubscription && $currentSubscription->ends_at)
                    <div class="overview-subvalue">
                        <i class="fa-solid fa-calendar"></i>
                        {{ $t('messages.expires_on') }}: {{ $currentSubscription->ends_at->format('d M Y') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Products Count Card -->
            <div class="overview-card products-card">
                <div class="overview-icon">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="overview-content">
                    <div class="overview-label">{{ $t('messages.products') ?? 'Products' }}</div>
                    <div class="overview-value">{{ $productsCount ?? 0 }}</div>
                    @if($currentPackage && $currentPackage->products_limit !== null)
                    <div class="overview-subvalue">
                        {{ $t('messages.limit') }}: {{ $currentPackage->products_limit }}
                    </div>
                    @else
                    <div class="overview-subvalue">{{ $t('messages.unlimited') }}</div>
                    @endif
                </div>
            </div>

            <!-- Pages Count Card -->
            <div class="overview-card pages-card">
                <div class="overview-icon">
                    <i class="fa-solid fa-link"></i>
                </div>
                <div class="overview-content">
                    <div class="overview-label">{{ $t('messages.pages') ?? 'Pages' }}</div>
                    <div class="overview-value">{{ $pagesCount ?? 0 }}</div>
                    @if($currentPackage && $currentPackage->pages_limit !== null)
                    <div class="overview-subvalue">
                        {{ $t('messages.limit') }}: {{ $currentPackage->pages_limit }}
                    </div>
                    @else
                    <div class="overview-subvalue">{{ $t('messages.unlimited') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Package Usage Section -->
    @if($currentPackage)
    <div class="package-usage-section">
        <h2 class="section-title">{{ $t('messages.package_usage') ?? 'Package Usage' }}</h2>
        <div class="usage-grid">
            <div class="usage-card">
                <div class="usage-header">
                    <span class="usage-label">{{ $t('messages.landing_pages') ?? 'Landing Pages' }}</span>
                    <span class="usage-value">{{ $pagesCount ?? 0 }}/{{ ($currentPackage->pages_limit !== null) ? $currentPackage->pages_limit : $t('messages.unlimited') }}</span>
                </div>
                @if($currentPackage->pages_limit !== null)
                <div class="usage-progress">
                    <div class="progress-bar" data-width="{{ $pagesUsage ?? 0 }}"></div>
                </div>
                @endif
            </div>

            <div class="usage-card">
                <div class="usage-header">
                    <span class="usage-label">{{ $t('messages.added_products') ?? 'Products' }}</span>
                    <span class="usage-value">{{ $productsCount ?? 0 }}/{{ ($currentPackage->products_limit !== null) ? $currentPackage->products_limit : $t('messages.unlimited') }}</span>
                </div>
                @if($currentPackage->products_limit !== null)
                <div class="usage-progress">
                    <div class="progress-bar" data-width="{{ $productsUsage ?? 0 }}"></div>
                </div>
                @endif
            </div>

            <div class="usage-card">
                <div class="usage-header">
                    <span class="usage-label">{{ $t('messages.daily_edits') ?? 'Daily Edits' }}</span>
                    <span class="usage-value">{{ $dailyEditsCount ?? 0 }}/{{ ($currentPackage->daily_orders_limit !== null) ? $currentPackage->daily_orders_limit : $t('messages.unlimited') }}</span>
                </div>
                @if($currentPackage->daily_orders_limit !== null)
                <div class="usage-progress">
                    <div class="progress-bar" data-width="{{ $dailyEditsUsage ?? 0 }}"></div>
                </div>
                @endif
            </div>

            <div class="usage-card">
                <div class="usage-header">
                    <span class="usage-label">{{ $t('messages.monthly_requests') ?? 'Monthly Requests' }}</span>
                    <span class="usage-value">{{ $monthlyOrdersCount ?? 0 }}/{{ ($currentPackage->monthly_orders_limit !== null) ? $currentPackage->monthly_orders_limit : $t('messages.unlimited') }}</span>
                </div>
                @if($currentPackage->monthly_orders_limit !== null)
                <div class="usage-progress">
                    <div class="progress-bar" data-width="{{ $monthlyOrdersUsage ?? 0 }}"></div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-chart-bar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.delivery_rate') }}</div>
                <div class="stat-value">{{ number_format($deliveryRate, 2) }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.delivered_orders') }}</div>
                <div class="stat-value">{{ $deliveredOrders }}</div>
                <div class="stat-subvalue">{{ number_format(($deliveredOrdersSales ?? 0) / 100, 2) }} {{ $t('messages.currency') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_sales') }}</div>
                <div class="stat-value">{{ number_format($totalSales / 100, 2) }} {{ $t('messages.currency') }}</div>
                <div class="stat-subvalue">{{ $t('messages.according_to_selected_currency') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_orders') }}</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_product') }}</h3>
            <div class="chart-placeholder">
                <canvas id="ordersByProductChart" data-chart-data="{{ json_encode($chartDataByProduct) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_status') }}</h3>
            <div class="chart-legend">
                @foreach($ordersByStatus as $statusItem)
                <div class="legend-item">
                    <span class="legend-dot legend-{{ $statusItem->status }}"></span>
                    <span class="legend-label">{{ $t('messages.order_status_' . $statusItem->status) }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-placeholder">
                <canvas id="ordersByStatusChart" data-chart-data="{{ json_encode($chartDataByStatus) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_landing_page') }}</h3>
            <div class="chart-placeholder">
                <canvas id="ordersByPageChart" data-chart-data="{{ json_encode($chartDataByPage) }}"></canvas>
            </div>
        </div>
    </div>
</div>

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script src="{{ asset('js/user-dashboard.js') }}"></script>
<script src="{{ asset('js/user-dashboard-progress.js') }}"></script>
@endpush
@endsection