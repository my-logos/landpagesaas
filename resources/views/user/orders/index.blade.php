@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Control Panel Section -->
    <div class="control-panel-section">
        <h2 class="control-panel-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h2>
    </div>

    <!-- Page Header -->
    <div class="orders-page-header">
        <h1 class="orders-main-title">{{ $t('messages.order_management') ?? 'Order Management' }}</h1>
        <div class="performance-mode-indicator">
            <i class="fa-solid fa-info-circle"></i>
            <span>{{ $t('messages.performance_mode') ?? 'Performance Mode' }}: {{ $t('messages.performance_mode_' . $performanceMode) ?? ucfirst($performanceMode) }}</span>
        </div>
    </div>

    <!-- Export Button -->
    <div class="export-button-wrapper">
        <a href="{{ route('user.orders.export') }}" class="btn-export-data">
            <i class="fa-solid fa-download"></i>
            {{ $t('messages.export_data') ?? 'Export Data' }}
        </a>
    </div>

    <!-- Filter Orders Section -->
    <div class="filter-card collapsible-card">
        <div class="filter-card-header collapsible-header" data-target="filter-body">
            <div class="header-left">
                <i class="fa-solid fa-filter"></i>
                <span>{{ $t('messages.filter_orders') ?? 'Filter Orders' }}</span>
            </div>
            <i class="fa-solid fa-chevron-down toggle-icon"></i>
        </div>
        <div class="filter-card-body collapsible-body" id="filter-body">
            <form method="GET" action="{{ route('user.orders.index') }}" class="filter-form" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group filter-group-full">
                        <input type="text"
                            name="search"
                            class="filter-input"
                            placeholder="{{ $t('messages.quick_search') ?? 'Quick Search...' }}"
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">{{ $t('messages.order_status') ?? 'Order Status' }}</label>
                        <select name="status" class="filter-select">
                            <option value="">{{ $t('messages.all_statuses') ?? 'All Statuses' }}</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ $t('messages.order_status_' . $status) ?? ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">{{ $t('messages.landing_page') ?? 'Landing Page' }}</label>
                        <select name="landing_page_id" class="filter-select">
                            <option value="">{{ $t('messages.all_pages') ?? 'All Pages' }}</option>
                            @foreach($pages as $page)
                            <option value="{{ $page->id }}" {{ request('landing_page_id') == $page->id ? 'selected' : '' }}>
                                {{ $page->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">{{ $t('messages.product') ?? 'Product' }}</label>
                        <select name="product_id" class="filter-select">
                            <option value="">{{ $t('messages.all_products') ?? 'All Products' }}</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">{{ $t('messages.start_date') ?? 'Start Date' }}</label>
                        <input type="date"
                            name="start_date"
                            class="filter-input"
                            value="{{ request('start_date') }}"
                            placeholder="{{ $t('messages.select_start_date') ?? 'Select Start Date' }}">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">{{ $t('messages.end_date') ?? 'End Date' }}</label>
                        <input type="date"
                            name="end_date"
                            class="filter-input"
                            value="{{ request('end_date') }}"
                            placeholder="{{ $t('messages.select_end_date') ?? 'Select End Date' }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filter">
                        <i class="fa-solid fa-filter"></i>
                        {{ $t('messages.apply_filter') ?? 'Apply Filter' }}
                    </button>
                    <a href="{{ route('user.orders.index') }}" class="btn-reset-filter">
                        <i class="fa-solid fa-rotate"></i>
                        {{ $t('messages.reset') ?? 'Reset' }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Settings Section -->
    <div class="performance-card collapsible-card">
        <div class="performance-card-header collapsible-header" data-target="performance-body">
            <div class="performance-header-left">
                <i class="fa-solid fa-gear"></i>
                <span>{{ $t('messages.performance_settings') ?? 'Performance Settings' }}</span>
                <span class="performance-mode-badge">
                    {{ $t('messages.performance_mode_' . $performanceMode) ?? ucfirst($performanceMode) }}
                    ({{ $t('messages.' . ($performanceMode === 'fast' ? 'basic_data_only' : ($performanceMode === 'selective' ? 'session_data_only' : 'all_data'))) ?? 'All Data' }})
                </span>
            </div>
            <div class="performance-header-right">
                <a href="{{ route('user.orders.index') }}" class="btn-refresh-small">
                    <i class="fa-solid fa-rotate"></i>
                </a>
                <i class="fa-solid fa-chevron-down toggle-icon"></i>
            </div>
        </div>
        <div class="performance-card-body collapsible-body" id="performance-body">
            <div class="performance-estimated-time">
                <i class="fa-solid fa-arrow-up"></i>
                {{ $t('messages.estimated_load_time') ?? 'Estimated Load Time' }}: {{ $estimatedLoadTime }}
            </div>

            <div class="performance-presets">
                <h4 class="performance-presets-title">{{ $t('messages.predefined_performance_modes') ?? 'Predefined Performance Modes' }}</h4>
                <div class="performance-presets-grid">
                    <div class="performance-preset {{ $performanceMode === 'fast' ? 'active' : '' }}" data-mode="fast">
                        <div class="preset-label">{{ $t('messages.fast') ?? 'Fast' }}</div>
                        <div class="preset-description">{{ $t('messages.basic_data_only') ?? 'Basic Data Only' }}</div>
                    </div>
                    <div class="performance-preset {{ $performanceMode === 'selective' ? 'active' : '' }}" data-mode="selective">
                        <div class="preset-label">{{ $t('messages.selective') ?? 'Selective' }}</div>
                        <div class="preset-description">{{ $t('messages.session_data_only') ?? 'Session Data Only' }}</div>
                    </div>
                    <div class="performance-preset {{ $performanceMode === 'full' ? 'active' : '' }}" data-mode="full">
                        <div class="preset-label">{{ $t('messages.full') ?? 'Full' }}</div>
                        <div class="preset-description">{{ $t('messages.all_data') ?? 'All Data' }}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('user.orders.performance-settings') }}" class="performance-settings-form" id="performanceForm">
                @csrf
                <input type="hidden" name="performance_mode" id="performance_mode" value="{{ $performanceMode }}">

                <div class="custom-settings">
                    <h4 class="custom-settings-title">{{ $t('messages.custom_settings') ?? 'Custom Settings' }}</h4>
                    <div class="custom-settings-list">
                        <label class="custom-setting-item">
                            <input type="checkbox"
                                name="include_session"
                                {{ $includeSessionData ? 'checked' : '' }}
                                id="include_session">
                            <span>{{ $t('messages.include_session_data') ?? 'Include Session Data (IP, Session ID)' }}</span>
                        </label>
                        <label class="custom-setting-item">
                            <input type="checkbox"
                                name="include_location"
                                {{ $includeLocation ? 'checked' : '' }}
                                id="include_location">
                            <span>{{ $t('messages.include_location_data') ?? 'Include Geographic Location Data (City)' }}</span>
                        </label>
                        <label class="custom-setting-item">
                            <input type="checkbox"
                                name="include_device"
                                {{ $includeDeviceType ? 'checked' : '' }}
                                id="include_device">
                            <span>{{ $t('messages.include_device_type') ?? 'Include Device Type Detection (Mobile, Tablet, Desktop)' }}</span>
                        </label>
                    </div>
                </div>

                <div class="performance-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <ul>
                        <li>{{ $t('messages.performance_info_fast') ?? 'Fast Mode: Loads basic data only (Order Number, Customer, Price)' }}</li>
                        <li>{{ $t('messages.performance_info_selective') ?? 'Selective Mode: Adds basic session data' }}</li>
                        <li>{{ $t('messages.performance_info_full') ?? 'Full Mode: Loads all data including location and device type' }}</li>
                        <li>{{ $t('messages.performance_info_custom') ?? 'You can customize settings according to your needs to improve performance' }}</li>
                    </ul>
                </div>

                <div class="performance-actions">
                    <button type="submit" class="btn-apply-settings">
                        {{ $t('messages.apply_settings') ?? 'Apply Settings' }}
                    </button>
                    <a href="{{ route('user.orders.index') }}?reset_performance=1" class="btn-reset-settings">
                        <i class="fa-solid fa-rotate"></i>
                        {{ $t('messages.reset_to_fast') ?? 'Reset, to Fast Mode' }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <div class="orders-list-section">
        @if($orders->count() > 0)
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>{{ $t('messages.order_id') ?? 'Order ID' }}</th>
                        <th>{{ $t('messages.date') ?? 'Date' }}</th>
                        <th>{{ $t('messages.product') ?? 'Product' }}</th>
                        <th>{{ $t('messages.quantity') ?? 'Quantity' }}</th>
                        <th>{{ $t('messages.total') ?? 'Total' }}</th>
                        <th>{{ $t('messages.status') ?? 'Status' }}</th>
                        <th>{{ $t('messages.shipping_status') ?? 'Shipping Status' }}</th>
                        @if($performanceMode !== 'fast')
                        <th>{{ $t('messages.landing_page') ?? 'Landing Page' }}</th>
                        @endif
                        @if($includeSessionData)
                        <th>{{ $t('messages.session_data') ?? 'Session Data' }}</th>
                        @endif
                        @if($includeLocation)
                        <th>{{ $t('messages.location') ?? 'Location' }}</th>
                        @endif
                        @if($includeDeviceType)
                        <th>{{ $t('messages.device_type') ?? 'Device Type' }}</th>
                        @endif
                        <th>{{ $t('messages.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y - H:i') }}</td>
                        <td>{{ $order->product ? $order->product->name : '-' }}</td>
                        <td>
                            <div class="quantity-info">
                                <span class="quantity-value">{{ $order->quantity ?? 1 }}</span>
                                @if($order->customer_size || $order->customer_color)
                                <div class="variations-info">
                                    @if($order->customer_size)
                                    <span class="variation-badge size-badge">
                                        <i class="fa-solid fa-ruler"></i>
                                        {{ $order->customer_size }}
                                    </span>
                                    @endif
                                    @if($order->customer_color)
                                    <span class="variation-badge color-badge">
                                        <i class="fa-solid fa-palette"></i>
                                        {{ $order->customer_color }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>{{ number_format($order->total_cents / 100, 2) }} {{ $order->currency ?? 'EGP' }}</td>
                        <td>
                            <span class="status-badge status-{{ $order->status ?? 'pending' }}">
                                {{ $t('messages.order_status_' . ($order->status ?? 'pending')) ?? ucfirst($order->status ?? 'pending') }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $order->shipping_status ?? 'pending' }}" id="shipping-status-{{ $order->id }}">
                                {{ $t('messages.shipping_status_' . ($order->shipping_status ?? 'pending')) ?? ucfirst($order->shipping_status ?? 'pending') }}
                            </span>
                        </td>
                        @if($performanceMode !== 'fast')
                        <td>{{ $order->landingPage ? $order->landingPage->title : '-' }}</td>
                        @endif
                        @if($includeSessionData)
                        <td class="session-data-cell">
                            @if($order->customer_ip || $order->customer_session_id)
                            <div class="session-info">
                                @if($order->customer_ip)
                                <div class="info-item">
                                    <i class="fa-solid fa-network-wired"></i>
                                    <span class="info-label">{{ $t('messages.ip_address') ?? 'IP' }}:</span>
                                    <span class="info-value">{{ $order->customer_ip }}</span>
                                </div>
                                @endif
                                @if($order->customer_session_id)
                                <div class="info-item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span class="info-label">{{ $t('messages.session_id') ?? 'Session ID' }}:</span>
                                    <span class="info-value session-id">{{ substr($order->customer_session_id, 0, 8) }}...</span>
                                </div>
                                @endif
                            </div>
                            @else
                            <span class="no-data">-</span>
                            @endif
                        </td>
                        @endif
                        @if($includeLocation)
                        <td class="location-cell">
                            @if($order->customer_city)
                            <div class="location-info">
                                <i class="fa-solid fa-map-marker-alt"></i>
                                <span>{{ $order->customer_city }}</span>
                            </div>
                            @else
                            <span class="no-data">-</span>
                            @endif
                        </td>
                        @endif
                        @if($includeDeviceType)
                        <td class="device-type-cell">
                            @if($order->customer_device_type)
                            <span class="device-badge device-{{ $order->customer_device_type }}">
                                @if($order->customer_device_type === 'mobile')
                                <i class="fa-solid fa-mobile-screen-button"></i>
                                {{ $t('messages.device_mobile') ?? 'Mobile' }}
                                @elseif($order->customer_device_type === 'tablet')
                                <i class="fa-solid fa-tablet-screen-button"></i>
                                {{ $t('messages.device_tablet') ?? 'Tablet' }}
                                @else
                                <i class="fa-solid fa-desktop"></i>
                                {{ $t('messages.device_desktop') ?? 'Desktop' }}
                                @endif
                            </span>
                            @else
                            <span class="no-data">-</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div class="status-update-dropdown">
                                <button type="button" class="btn-status-update" data-order-id="{{ $order->id }}">
                                    <i class="fa-solid fa-edit"></i> {{ $t('messages.update_status') ?? 'Update' }}
                                </button>
                                <div class="status-dropdown" id="status-dropdown-{{ $order->id }}">
                                    <form action="{{ route('user.orders.update-shipping-status', $order) }}" method="POST" class="status-form">
                                        @csrf
                                        @method('PUT')
                                        <select name="shipping_status" class="form-control status-select" id="shipping-status-select-{{ $order->id }}">
                                            <option value="pending" {{ ($order->shipping_status ?? 'pending') === 'pending' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_pending') }}
                                            </option>
                                            <option value="processing" {{ ($order->shipping_status ?? 'pending') === 'processing' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_processing') }}
                                            </option>
                                            <option value="shipped" {{ ($order->shipping_status ?? 'pending') === 'shipped' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_shipped') }}
                                            </option>
                                            <option value="delivered" {{ ($order->shipping_status ?? 'pending') === 'delivered' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_delivered') }}
                                            </option>
                                            <option value="rejected" {{ ($order->shipping_status ?? 'pending') === 'rejected' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_rejected') }}
                                            </option>
                                            <option value="failed_delivery" {{ ($order->shipping_status ?? 'pending') === 'failed_delivery' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_failed_delivery') }}
                                            </option>
                                            <option value="postponed" {{ ($order->shipping_status ?? 'pending') === 'postponed' ? 'selected' : '' }}>
                                                {{ $t('messages.shipping_status_postponed') }}
                                            </option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-orders-state">
            <div class="empty-icon">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h3 class="empty-title">{{ $t('messages.no_orders') ?? 'No Orders' }}</h3>
            <p class="empty-description">
                {{ $t('messages.no_orders_description') ?? 'No orders have been created yet. Start by creating a product and a landing page to receive orders.' }}
            </p>
        </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-orders.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/user-orders.js') }}"></script>
@endpush
@endsection