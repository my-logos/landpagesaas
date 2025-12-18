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
            <h1 class="page-title">{{ $t('messages.product_details') ?? 'تفاصيل المنتج' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ $t('messages.back') ?? 'رجوع' }}
            </a>
        </div>
    </div>

    <div class="details-container">
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.product_information') ?? 'Product Information' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.product_name') ?? 'Product Name' }}</label>
                    <span>{{ $product->name }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.price') ?? 'Price' }}</label>
                    <span>{{ number_format($product->price_cents / 100, 2) }} {{ $t('messages.currency') ?? 'EGP' }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.ai_version') ?? 'AI Version' }}</label>
                    <span class="badge badge-info">{{ strtoupper($product->ai_version ?? 'gemini') }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.created_at') ?? 'Created At' }}</label>
                    <span>{{ $product->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>

        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.user_information') ?? 'User Information' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.user_name') ?? 'User Name' }}</label>
                    <span>{{ $product->user->name ?? '-' }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.email') ?? 'Email' }}</label>
                    <span>{{ $product->user->email ?? '-' }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.phone') ?? 'Phone' }}</label>
                    <span>{{ $product->user->phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.orders_statistics') ?? 'Orders Statistics' }}</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>{{ $t('messages.total_orders') ?? 'Total Orders' }}</label>
                    <span class="badge badge-primary">{{ $ordersCount }}</span>
                </div>
                <div class="detail-item">
                    <label>{{ $t('messages.total_revenue') ?? 'Total Revenue' }}</label>
                    <span class="badge badge-success">{{ number_format($totalRevenue / 100, 2) }} {{ $t('messages.currency') ?? 'EGP' }}</span>
                </div>
            </div>
        </div>

        @if($product->orders->count() > 0)
        <div class="details-card">
            <h2 class="card-title">{{ $t('messages.orders_list') ?? 'Orders List' }}</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $t('messages.order_id') ?? 'Order ID' }}</th>
                            <th>{{ $t('messages.quantity') ?? 'Quantity' }}</th>
                            <th>{{ $t('messages.total') ?? 'Total' }}</th>
                            <th>{{ $t('messages.status') ?? 'Status' }}</th>
                            <th>{{ $t('messages.created_at') ?? 'Created At' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->total_cents / 100, 2) }} {{ $t('messages.currency') ?? 'EGP' }}</td>
                            <td>
                                <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection