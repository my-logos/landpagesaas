@extends('layouts.guest')

@section('content')
<div class="order-track-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="track-container">
        <div class="header">
            <div class="header-icon">
                <i class="fa-solid fa-box"></i>
            </div>
            <h1>{{ app()->getLocale() === 'ar' ? 'تتبع حالة الطلب' : 'Track Order Status' }}</h1>
            <div class="order-number">#{{ $order->order_number }}</div>
        </div>

        <div class="status-section">
            <div class="status-title">{{ app()->getLocale() === 'ar' ? 'حالة الطلب' : 'Order Status' }}</div>
            <div class="status-badge status-{{ $order->status }}">
                @if(app()->getLocale() === 'ar')
                @if($order->status === 'pending') قيد الانتظار
                @elseif($order->status === 'processing') قيد المعالجة
                @elseif($order->status === 'shipped') تم الشحن
                @elseif($order->status === 'delivered') تم التوصيل
                @elseif($order->status === 'rejected') مرفوض
                @else {{ ucfirst($order->status) }}
                @endif
                @else
                {{ ucfirst($order->status) }}
                @endif
            </div>

            @if($order->shipping_status && $order->shipping_status !== $order->status)
            <div class="status-title shipping-status">{{ app()->getLocale() === 'ar' ? 'حالة الشحن' : 'Shipping Status' }}</div>
            <div class="status-badge status-{{ $order->shipping_status }}">
                @if(app()->getLocale() === 'ar')
                @if($order->shipping_status === 'pending') قيد الانتظار
                @elseif($order->shipping_status === 'processing') قيد المعالجة
                @elseif($order->shipping_status === 'shipped') تم الشحن
                @elseif($order->shipping_status === 'delivered') تم التوصيل
                @elseif($order->shipping_status === 'rejected') مرفوض
                @else {{ ucfirst($order->shipping_status) }}
                @endif
                @else
                {{ ucfirst($order->shipping_status) }}
                @endif
            </div>
            @endif
        </div>

        <div class="order-info">
            @if($order->product)
            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'المنتج' : 'Product' }}</span>
                <span class="info-value">{{ $order->product->name }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }}</span>
                <span class="info-value">{{ $order->quantity }}</span>
            </div>

            @if($size)
            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'المقاس' : 'Size' }}</span>
                <span class="info-value">{{ $size }}</span>
            </div>
            @endif

            @if($color)
            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'اللون' : 'Color' }}</span>
                <span class="info-value">{{ $color }}</span>
            </div>
            @endif

            @if($order->total_cents > 0)
            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'المجموع' : 'Total' }}</span>
                <span class="info-value">{{ number_format($order->total_cents / 100, 2) }} {{ $order->currency ?? 'EGP' }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الطلب' : 'Order Date' }}</span>
                <span class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
            </div>

            @if($order->customer_data && is_array($order->customer_data))
            <div class="customer-data">
                <h3>{{ app()->getLocale() === 'ar' ? 'بيانات العميل' : 'Customer Information' }}</h3>
                @foreach($order->customer_data as $key => $value)
                @if(!in_array($key, ['size', 'color', 'ip', 'session_id', 'city', 'device_type']))
                <div class="info-row">
                    <span class="info-label">{{ app()->getLocale() === 'ar' ? ucfirst(str_replace(['_', 'name', 'phone', 'address'], [' ', 'الاسم', 'الهاتف', 'العنوان'], $key)) : ucfirst(str_replace('_', ' ', $key)) }}</span>
                    <span class="info-value">{{ $value }}</span>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        <div class="action-buttons">
            @if(isset($existingMessage) && $existingMessage)
            <a href="{{ route('public.message.create', ['order_number' => $order->order_number, 'message_id' => $existingMessage->id]) }}" class="btn btn-primary">
                <i class="fa-solid fa-envelope-open"></i>
                {{ app()->getLocale() === 'ar' ? 'عرض الرسائل السابقة' : 'View Previous Messages' }}
            </a>
            @else
            <a href="{{ route('public.message.create', ['order_number' => $order->order_number]) }}" class="btn btn-primary">
                <i class="fa-solid fa-envelope"></i>
                {{ app()->getLocale() === 'ar' ? 'إرسال رسالة' : 'Send Message' }}
            </a>
            @endif
            @if($landingPage)
            <a href="{{ route('page.show', $landingPage->id) }}" class="btn btn-secondary">
                <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                {{ app()->getLocale() === 'ar' ? 'العودة للصفحة' : 'Back to Page' }}
            </a>
            @endif
        </div>

        @if($additionalSalesEnabled && $processedAdditionalProducts && count($processedAdditionalProducts) > 0)
        <!-- Additional Products Section -->
        <div class="additional-products-section">
            <h2 class="section-title">{{ app()->getLocale() === 'ar' ? 'تسوق المزيد' : 'Shop More' }}</h2>
            <div class="products-grid">
                @foreach($processedAdditionalProducts as $product)
                <div class="product-card">
                    @if($product['first_image'])
                    <div class="product-image">
                        <img src="{{ $product['first_image'] }}" alt="{{ $product['name'] }}">
                    </div>
                    @endif
                    <div class="product-info">
                        <h3 class="product-name">{{ $product['name'] }}</h3>
                        @if($product['short_description'])
                        <p class="product-description">{{ \Illuminate\Support\Str::limit($product['short_description'], 80) }}</p>
                        @endif
                        <div class="product-price">
                            {{ number_format($product['price_cents'] / 100, 2) }} {{ $product['currency'] }}
                        </div>
                        @if($product['page_id'])
                        <a href="{{ route('page.show', $product['page_id']) }}" class="btn-view-product">
                            {{ app()->getLocale() === 'ar' ? 'عرض المنتج' : 'View Product' }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection