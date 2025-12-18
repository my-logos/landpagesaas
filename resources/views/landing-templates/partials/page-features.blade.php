@if($fixedOrderButtonEnabled)
<!-- Fixed Order Button -->
<a href="#order-form" class="fixed-order-button fixed-order-button-{{ $buttonPosition }} fixed-order-button-{{ $buttonShape }} fixed-order-button-{{ $buttonSize }}" data-button-color="{{ $buttonColor }}">
    <i class="fa-solid fa-{{ $buttonIcon }}"></i>
    <span>{{ $buttonText }}</span>
</a>
@endif

@if($smartCouponsEnabled && $page->product)
<!-- Smart Coupons System -->
<div class="smart-coupon-banner hidden" id="smart-coupon-banner">
    <div class="coupon-content">
        <div class="coupon-icon">
            <i class="fa-solid fa-tag"></i>
        </div>
        <div class="coupon-text">
            <span class="coupon-label">{{ app()->getLocale() === 'ar' ? 'خصم حصري!' : 'Exclusive Discount!' }}</span>
            <span class="coupon-value">{{ app()->getLocale() === 'ar' ? 'خصم 25%' : '25% OFF' }}</span>
        </div>
        <button class="coupon-close">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>
@endif

@if($remainingItemsCounterEnabled)
<!-- Remaining Items Counter -->
<div class="remaining-items-counter">
    <div class="counter-icon">
        <i class="fa-solid fa-fire"></i>
    </div>
    <div class="counter-content">
        <span class="counter-label">{{ app()->getLocale() === 'ar' ? 'القطع المتبقية' : 'Items Remaining' }}</span>
        <span class="counter-value">{{ $remainingItems }}</span>
    </div>
</div>
@endif