@if($fixedOrderButtonEnabled)
<!-- Fixed Order Button -->
<a href="#order-form" class="fixed-order-button fixed-order-button-{{ $buttonPosition }} fixed-order-button-{{ $buttonShape }} fixed-order-button-{{ $buttonSize }}"
    style="background-color: {{ $buttonColor }};">
    <i class="fa-solid fa-{{ $buttonIcon }}"></i>
    <span>{{ $buttonText }}</span>
</a>
@endif

@if($smartCouponsEnabled && $page->product)
<!-- Smart Coupons System -->
<div class="smart-coupon-banner" id="smart-coupon-banner" style="display: none;">
    <div class="coupon-content">
        <div class="coupon-icon">
            <i class="fa-solid fa-tag"></i>
        </div>
        <div class="coupon-text">
            <span class="coupon-label">{{ app()->getLocale() === 'ar' ? 'خصم حصري!' : 'Exclusive Discount!' }}</span>
            <span class="coupon-value">{{ app()->getLocale() === 'ar' ? 'خصم 25%' : '25% OFF' }}</span>
        </div>
        <button class="coupon-close" onclick="document.getElementById('smart-coupon-banner').style.display='none'">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>

<script>
    // Show coupon banner after 3 seconds
    setTimeout(function() {
        var banner = document.getElementById('smart-coupon-banner');
        if (banner) {
            banner.style.display = 'block';
        }
    }, 3000);
</script>
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

<style>
    /* Fixed Order Button Styles */
    .fixed-order-button {
        position: fixed;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 25px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .fixed-order-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    .fixed-order-button i {
        font-size: 18px;
    }

    /* Positions */
    .fixed-order-button-bottom-right {
        bottom: 20px;
        right: 20px;
    }

    .fixed-order-button-bottom-left {
        bottom: 20px;
        left: 20px;
    }

    .fixed-order-button-top-right {
        top: 20px;
        right: 20px;
    }

    .fixed-order-button-top-left {
        top: 20px;
        left: 20px;
    }

    /* Shapes */
    .fixed-order-button-rounded {
        border-radius: 25px;
    }

    .fixed-order-button-square {
        border-radius: 8px;
    }

    .fixed-order-button-circle {
        border-radius: 50%;
        width: 60px;
        height: 60px;
        padding: 0;
        justify-content: center;
    }

    .fixed-order-button-circle span {
        display: none;
    }

    /* Sizes */
    .fixed-order-button-small {
        padding: 10px 18px;
        font-size: 14px;
    }

    .fixed-order-button-medium {
        padding: 15px 25px;
        font-size: 16px;
    }

    .fixed-order-button-large {
        padding: 18px 30px;
        font-size: 18px;
    }

    /* Pulse Animation */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    /* Smart Coupon Banner */
    .smart-coupon-banner {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 15px 25px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4);
        animation: slideDown 0.5s ease-out;
        max-width: 90%;
    }

    .coupon-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .coupon-icon {
        font-size: 24px;
    }

    .coupon-text {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .coupon-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .coupon-value {
        font-size: 20px;
        font-weight: 700;
    }

    .coupon-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }

    .coupon-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    @keyframes slideDown {
        from {
            transform: translateX(-50%) translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }

    /* Remaining Items Counter */
    .remaining-items-counter {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9998;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: shake 2s infinite;
    }

    body[dir="rtl"] .remaining-items-counter {
        right: auto;
        left: 20px;
    }

    .counter-icon {
        font-size: 20px;
        animation: fire 1s infinite;
    }

    .counter-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .counter-label {
        font-size: 12px;
        opacity: 0.9;
    }

    .counter-value {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    @keyframes fire {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    @media (max-width: 768px) {
        .fixed-order-button {
            padding: 12px 20px;
            font-size: 14px;
        }

        .fixed-order-button-circle {
            width: 50px;
            height: 50px;
        }

        .smart-coupon-banner {
            top: 10px;
            padding: 12px 20px;
            max-width: 95%;
        }

        .remaining-items-counter {
            top: 60px;
            right: 10px;
            padding: 10px 15px;
        }

        body[dir="rtl"] .remaining-items-counter {
            left: 10px;
        }
    }
</style>