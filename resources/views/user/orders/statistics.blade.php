@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.control_panel') ?? 'لوحة التحكم' }}</h1>
        </div>
        <div class="page-header-right">
            <div class="date-range-selector">
                <form method="GET" action="{{ route('user.orders.statistics') }}" class="date-filter-form">
                    <div class="date-input-group">
                        <label>{{ $t('messages.date_from') ?? 'التاريخ من:' }}:</label>
                        <input type="date" name="date_from" class="date-input" value="{{ $dateFrom ?? '' }}" />
                        <i class="fa-solid fa-calendar date-icon"></i>
                    </div>
                    <div class="date-input-group">
                        <label>{{ $t('messages.date_to') ?? 'التاريخ إلى:' }}:</label>
                        <input type="date" name="date_to" class="date-input" value="{{ $dateTo ?? '' }}" />
                        <i class="fa-solid fa-calendar date-icon"></i>
                    </div>
                    <button type="submit" class="btn-apply-date-filter">
                        {{ $t('messages.apply') ?? 'تطبيق' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.delivery_rate') ?? 'نسبة التوصيل' }}</div>
                <div class="stat-value">{{ number_format($deliveryRate, 2) }}%</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.delivered_orders') ?? 'الطلبات الموصلة' }}</div>
                <div class="stat-value">{{ $deliveredOrders }}</div>
                <div class="stat-subvalue">{{ number_format(($deliveredOrdersSales ?? 0) / 100, 2) }} {{ $t('messages.currency') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_sales') ?? 'إجمالي المبيعات' }}</div>
                <div class="stat-value">{{ number_format($totalSales / 100, 2) }} {{ $t('messages.currency') }}</div>
                <div class="stat-subvalue">{{ $t('messages.according_to_selected_currency') ?? 'حسب العملة المختارة' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ $t('messages.total_orders') ?? 'مجموع الطلبات' }}</div>
                <div class="stat-value">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_product') ?? 'توزيع الطلبات حسب المنتج' }}</h3>
            <div class="chart-placeholder">
                <canvas id="ordersByProductChart" data-chart-data="{{ json_encode($chartDataByProduct) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_status') ?? 'توزيع الطلبات حسب الحالة' }}</h3>
            <div class="chart-legend">
                @foreach($ordersByStatus as $statusItem)
                <div class="legend-item">
                    <span class="legend-dot legend-{{ $statusItem->status }}"></span>
                    <span class="legend-label">{{ $t('messages.order_status_' . $statusItem->status) ?? $statusItem->status }}</span>
                </div>
                @endforeach
            </div>
            <div class="chart-placeholder">
                <canvas id="ordersByStatusChart" data-chart-data="{{ json_encode($chartDataByStatus) }}"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3 class="chart-title">{{ $t('messages.orders_distribution_by_landing_page') ?? 'توزيع الطلبات حسب صفحة الهبوط' }}</h3>
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
@endpush
@endsection