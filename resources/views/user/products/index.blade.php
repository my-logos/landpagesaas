@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <!-- Header Section -->
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $t('messages.products') ?? 'المنتجات' }}</h1>
        </div>
        <div class="page-header-right">
            <a href="{{ route('user.products.create') }}" class="btn-add-product">
                <i class="fa-solid fa-plus"></i>
                <span>{{ $t('messages.add_new_product') ?? 'إضافة منتج جديد' }}</span>
            </a>
            <div class="view-toggle">
                <button type="button" class="view-btn view-list" data-view="list">
                    <i class="fa-solid fa-list"></i>
                </button>
                <button type="button" class="view-btn view-grid active" data-view="grid">
                    <i class="fa-solid fa-th"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Advanced Search and Filtering Section -->
    <div class="products-filter-section">
        <div class="filter-header" id="filterToggle">
            <div class="filter-header-title">
                <i class="fa-solid fa-filter"></i>
                <span>{{ $t('messages.advanced_search_filter') ?? 'البحث والتصفية المتقدمة' }}</span>
            </div>
            <i class="fa-solid fa-chevron-down" id="filterChevron"></i>
        </div>

        <div class="filter-content collapsed" id="filterContent">
            <form method="GET" action="{{ route('user.products.index') }}" id="filterForm">
                <div class="filter-form-grid">
                    <!-- Creation Date From -->
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-calendar"></i>
                            {{ $t('messages.creation_date_from') ?? 'تاريخ الإنشاء من' }}
                        </label>
                        <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control">
                    </div>

                    <!-- Creation Date To -->
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-calendar"></i>
                            {{ $t('messages.creation_date_to') ?? 'تاريخ الإنشاء إلى' }}
                        </label>
                        <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
                    </div>

                    <!-- Currency Filter -->
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-dollar-sign"></i>
                            {{ $t('messages.currency') ?? 'العملة' }}
                        </label>
                        <select name="currency" class="form-control">
                            <option value="">{{ $t('messages.all_currencies') ?? 'جميع العملات' }}</option>
                            <option value="EGP" {{ request('currency') == 'EGP' ? 'selected' : '' }}>EGP</option>
                            <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ request('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="SAR" {{ request('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                            <option value="AED" {{ request('currency') == 'AED' ? 'selected' : '' }}>AED</option>
                        </select>
                    </div>

                    <!-- Price Range - Minimum -->
                    <div class="filter-group">
                        <label>{{ $t('messages.minimum_price') ?? 'السعر الأدنى' }}</label>
                        <input type="number" name="price_min" value="{{ request('price_min') }}" class="form-control" placeholder="0">
                    </div>

                    <!-- Price Range - Maximum -->
                    <div class="filter-group">
                        <label>{{ $t('messages.maximum_price') ?? 'السعر الأعلى' }}</label>
                        <input type="number" name="price_max" value="{{ request('price_max') }}" class="form-control" placeholder="999999">
                    </div>
                </div>

                <!-- Search and Taager Filter Row -->
                <div class="filter-form-row">
                    <!-- Product Search -->
                    <div class="filter-group">
                        <label>
                            <i class="fa-solid fa-search"></i>
                            {{ $t('messages.search_in_products') ?? 'البحث في المنتجات' }}
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ $t('messages.search_product_name_description') ?? 'ابحث في اسم أو وصف المنتج...' }}">
                    </div>

                    <!-- Taager Link Status -->
                    <div class="filter-group">
                        <label>{{ $t('messages.taager_link_status') ?? 'حالة ربط Taager' }}</label>
                        <select name="taager_status" class="form-control">
                            <option value="">{{ $t('messages.all_products') ?? 'جميع المنتجات' }}</option>
                            <option value="linked" {{ request('taager_status') == 'linked' ? 'selected' : '' }}>{{ $t('messages.linked') ?? 'مرتبط' }}</option>
                            <option value="not_linked" {{ request('taager_status') == 'not_linked' ? 'selected' : '' }}>{{ $t('messages.not_linked') ?? 'غير مرتبط' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Action Buttons -->
                <div class="filter-actions">
                    <button type="submit" class="btn-apply-filters">
                        <i class="fa-solid fa-filter"></i>
                        <span>{{ $t('messages.apply_filters') ?? 'تطبيق الفلاتر' }}</span>
                    </button>
                    <button type="button" class="btn-clear-filters">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>{{ $t('messages.clear_all_filters') ?? 'مسح جميع الفلاتر' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid" id="products-container">
        @forelse($products as $product)
        <div class="product-card">
            <div class="product-image-container">
                @if(!empty($product->processed_images) && count($product->processed_images) > 0)
                <img src="{{ $product->processed_images[0] }}" alt="{{ $product->name }}">
                @else
                <div class="product-image-placeholder">
                    <div>
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>ECOMMERCE</div>
                </div>
                @endif
                <button type="button" class="product-edit-btn" data-product-id="{{ $product->id }}">
                    <i class="fa-solid fa-pencil"></i>
                </button>
            </div>
            <div class="product-info">
                <h3 class="product-name">{{ $product->name }}</h3>
                <p class="product-description">{{ Str::limit($product->short_description ?? '', 100) }}</p>
                <div class="product-price">{{ $product->currency ?? 'EGP' }} {{ number_format($product->price_cents / 100, 0) }}</div>
                <div class="product-shipping">{{ $t('messages.shipping') ?? 'شحن' }}: {{ $product->currency ?? 'EGP' }} {{ number_format($product->shipping_price_cents / 100, 0) }}</div>
                <div class="product-date">{{ $product->created_at->format('Y/m/d') }}</div>

                <!-- Product Actions Buttons -->
                <div class="product-actions">

                    @if($product->has_landing_page && $product->first_landing_page)
                    <a href="{{ route('page.show', $product->first_landing_page->id) }}" target="_blank" class="btn-landing-page">
                        <i class="fa-solid fa-external-link"></i>
                        <span>{{ $t('messages.view_landing_page') ?? 'صفحة هبوط' }}</span>
                    </a>
                    @else
                    <a href="{{ route('user.pages.create', ['product_id' => $product->id]) }}" class="btn-create-landing-page">
                        <i class="fa-solid fa-plus"></i>
                        <span>{{ $t('messages.create_landing_page') ?? 'إنشاء صفحة هبوط' }}</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="no-products">
            <i class="fa-solid fa-box-open"></i>
            <p>{{ $t('messages.no_products') ?? 'لا توجد منتجات' }}</p>
        </div>
        @endforelse
    </div>
</div>

@push('styles')
<!-- Styles included in main.css -->
@endpush

@push('scripts')
<script src="{{ asset('js/products-view.js') }}"></script>
@endpush
@endsection