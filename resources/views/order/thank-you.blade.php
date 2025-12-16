<!doctype html>
<html lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $locale === 'ar' ? 'شكراً لك - تم استلام طلبك' : 'Thank You - Order Received' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    @if($locale === 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/order-thank-you.css') }}">
    <style>
        :root {
            --font-family: {{ $locale === 'ar' ? "'Tajawal', sans-serif" : "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" }};
        }
    </style>
</head>

<body class="order-thank-you-page" dir="{{ $dir }}">
    <div class="thank-you-container">
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1>{{ $locale === 'ar' ? 'شكراً لك!' : 'Thank You!' }}</h1>
        <p class="subtitle">{{ $locale === 'ar' ? 'تم استلام طلبك بنجاح' : 'Your order has been received successfully' }}</p>

        <div class="order-info">
            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'رقم الطلب' : 'Order Number' }}</span>
                <span class="info-value order-number">{{ $order->order_number }}</span>
            </div>

            @if($order->product)
            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'المنتج' : 'Product' }}</span>
                <span class="info-value">{{ $order->product->name }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'الكمية' : 'Quantity' }}</span>
                <span class="info-value">{{ $order->quantity }}</span>
            </div>

            @if($size)
            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'المقاس' : 'Size' }}</span>
                <span class="info-value">{{ $size }}</span>
            </div>
            @endif

            @if($color)
            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'اللون' : 'Color' }}</span>
                <span class="info-value">{{ $color }}</span>
            </div>
            @endif

            @if($order->total_cents > 0)
            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'المجموع' : 'Total' }}</span>
                <span class="info-value">{{ number_format($order->total_cents / 100, 2) }} {{ $order->currency ?? 'EGP' }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">{{ $locale === 'ar' ? 'التاريخ' : 'Date' }}</span>
                <span class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
            </div>

            @if($order->customer_data && is_array($order->customer_data))
            <div class="customer-data">
                <h3>{{ $locale === 'ar' ? 'بيانات العميل' : 'Customer Information' }}</h3>
                @foreach($order->customer_data as $key => $value)
                @if(!in_array($key, ['size', 'color', 'ip', 'session_id', 'city', 'device_type']))
                <div class="data-item">
                    <span class="data-label">{{ $locale === 'ar' ? ucfirst(str_replace(['_', 'name', 'phone', 'address'], [' ', 'الاسم', 'الهاتف', 'العنوان'], $key)) : ucfirst(str_replace('_', ' ', $key)) }}</span>
                    <span class="data-value">{{ $value }}</span>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        <div class="action-buttons">
            <a href="{{ $trackingUrl }}" class="btn btn-primary">
                <i class="fa-solid fa-search"></i>
                {{ $locale === 'ar' ? 'تتبع حالة الطلب' : 'Track Order Status' }}
            </a>
            @if($landingPage)
            <a href="{{ route('page.show', $landingPage->id) }}" class="btn btn-secondary">
                <i class="fa-solid {{ $dir === 'rtl' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                {{ $locale === 'ar' ? 'العودة للصفحة' : 'Back to Page' }}
            </a>
            @endif
        </div>

        @if($additionalSalesEnabled && $processedAdditionalProducts && count($processedAdditionalProducts) > 0)
        <!-- Additional Products Section -->
        <div class="additional-products-section">
            <h2 class="section-title">{{ $locale === 'ar' ? 'تسوق المزيد' : 'Shop More' }}</h2>
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
                            {{ $locale === 'ar' ? 'عرض المنتج' : 'View Product' }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</body>

</html>