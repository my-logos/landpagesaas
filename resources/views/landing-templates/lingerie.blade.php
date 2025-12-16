@php
$templateCss = 'css/landing-lingerie.css';
$additionalFonts = '
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;600;700;800&display=swap" rel="stylesheet">';
@endphp
@include('landing-templates.partials.head')

<body data-google-analytics-id="{{ $page->google_analytics_id ?? '' }}"
    data-facebook-pixel="{{ $page->facebook_pixel ?? '' }}"
    data-tiktok-pixel="{{ $page->tiktok_pixel ?? '' }}"
    data-snapchat-pixel="{{ $page->snapchat_pixel ?? '' }}">

    @include('landing-templates.partials.page-features')
    <!-- Top Banner -->
    <div class="top-banner">
        {{ $page->additional_description ?? 'عرض حصري على اللانجيري! احصلي على خصم فوري، العرض محدود!' }}
    </div>

    <!-- Hero Section - Soft Minimal Design -->
    <section class="hero-section hero-soft-minimal">
        <div class="container">
            <div class="hero-soft-wrapper">
                <div class="hero-soft-image-wrapper">
                    @if($mainImage)
                    <img src="{{ $mainImage }}" alt="{{ $page->product->name ?? 'Product' }}" class="hero-soft-image">
                    @else
                    <div class="hero-soft-placeholder">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    @endif
                </div>
                <div class="hero-soft-content">
                    <div class="hero-badge-soft">
                        <i class="fa-solid fa-heart"></i>
                        <span>أناقة وراحة</span>
                    </div>
                    <h1 class="hero-title-soft">{{ $page->title ?? ($page->product->name ?? 'لانجيري أنيق') }}</h1>
                    <p class="hero-description-soft">
                        {{ $page->content ?? ($page->product->description ?? 'اكتشفي مجموعتنا المميزة من اللانجيري الأنيق. جودة عالية وراحة لا مثيل لها.') }}
                    </p>
                    @if($page->product)
                    <div class="pricing-soft">
                        <div class="price-main-soft">
                            <span class="current-price-soft">EGP {{ number_format($finalPrice, 2) }}</span>
                            <span class="old-price-soft">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                        </div>
                        <span class="discount-soft">خصم {{ round((($page->product->price_cents - ($page->product->price_cents * 0.75)) / $page->product->price_cents) * 100) }}%</span>
                    </div>
                    @endif
                    <div class="countdown" data-hours="24">
                        <p class="countdown-text">لا تفوتي الفرصة العرض ينتهي قريباً ....</p>
                        <div class="countdown-boxes">
                            <div class="countdown-box">00</div>
                            <div class="countdown-box">00</div>
                            <div class="countdown-box">00</div>
                        </div>
                        <div class="countdown-labels">
                            <span>ساعة</span>
                            <span>دقيقة</span>
                            <span>ثانية</span>
                        </div>
                    </div>
                    <a href="#order-form" class="btn-soft-cta">
                        <i class="fa-solid fa-heart"></i>
                        احصلي على منتجك الآن
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">لماذا تختارين مجموعتنا؟</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                    <h3 class="feature-title">جودة فاخرة</h3>
                    <p class="feature-description">خامات عالية الجودة من أفضل المصادر</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3 class="feature-title">تصميمات عصرية</h3>
                    <p class="feature-description">تصميمات أنيقة تناسب جميع الأذواق</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">خصوصية تامة</h3>
                    <p class="feature-description">تعبئة خاصة تضمن الخصوصية الكاملة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3 class="feature-title">تسليم سريع</h3>
                    <p class="feature-description">تسليم سريع ومحترف لجميع المحافظات</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Gallery Section -->
    @if(!empty($galleryImages))
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">معرض المنتجات</h2>
            <div class="gallery-grid">
                @foreach($galleryImages as $image)
                <div class="gallery-item">
                    <img src="{{ $image }}" alt="Gallery Image">
                    <div class="gallery-overlay">
                        <i class="fa-solid fa-search-plus"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Product Details Section -->
    <section class="product-details-section">
        <div class="container">
            <div class="product-details-content">
                <div class="details-image">
                    @if($detailImage)
                    <img src="{{ $detailImage }}" alt="Product Details">
                    @endif
                </div>
                <div class="details-text">
                    <h2 class="details-title">مميزات المنتجات</h2>
                    <ul class="details-list">
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-leaf"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>خامات طبيعية</strong>
                                <span>خامات ناعمة وطبيعية تضمن الراحة الكاملة</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-ruler"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>مقاسات متنوعة</strong>
                                <span>متوفر بجميع المقاسات لتناسب جميع الأجسام</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-palette"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>ألوان متنوعة</strong>
                                <span>مجموعة واسعة من الألوان الأنثوية الأنيقة</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>تعبئة مميزة</strong>
                                <span>تعبئة أنيقة ومميزة للهدايا</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="why-choose-section">
        <div class="container">
            <h2 class="section-title">لماذا نحن الأفضل؟</h2>
            <div class="why-choose-grid">
                <div class="why-choose-card">
                    <div class="why-choose-number">01</div>
                    <h3 class="why-choose-title">جودة فاخرة</h3>
                    <p class="why-choose-description">خامات عالية الجودة من أفضل المصادر العالمية</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">02</div>
                    <h3 class="why-choose-title">أسعار مناسبة</h3>
                    <p class="why-choose-description">أفضل الأسعار مع عروض وخصومات مستمرة</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">03</div>
                    <h3 class="why-choose-title">خصوصية تامة</h3>
                    <p class="why-choose-description">تعبئة خاصة تضمن الخصوصية الكاملة</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">04</div>
                    <h3 class="why-choose-title">ضمان الجودة</h3>
                    <p class="why-choose-description">نضمن رضاك التام أو استرداد كامل المبلغ</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">آراء عملائنا</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-pink">س</div>
                        <div class="testimonial-info">
                            <h4>سارة أحمد</h4>
                            <p>(القاهرة)</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">"جودة ممتازة وتصميمات أنيقة. الخامات ناعمة جداً والراحة لا مثيل لها. التعبئة كانت خاصة ومميزة."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-rose">م</div>
                        <div class="testimonial-info">
                            <h4>مريم علي</h4>
                            <p>(الجيزة)</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">"أفضل متجر لانجيري. الجودة عالية والأسعار معقولة. الخصوصية مضمونة والتعبئة أنيقة."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-coral">ف</div>
                        <div class="testimonial-info">
                            <h4>فاطمة محمود</h4>
                            <p>(الإسكندرية)</p>
                        </div>
                    </div>
                    <div class="testimonial-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="testimonial-text">"خدمة رائعة وجودة فاخرة. التوصيل كان سريع والتعبئة خاصة. سأشتري مرة أخرى بالتأكيد."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">أسئلة متكررة (FAQ)</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل التعبئة خاصة ومميزة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، جميع المنتجات يتم تعبئتها بشكل خاص ومميز لضمان الخصوصية التامة.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>ما هي المقاسات المتاحة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>متوفر بجميع المقاسات من XS إلى XXL لتناسب جميع الأجسام.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>ما هي طرق الدفع المتاحة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نقبل الدفع نقداً عند الاستلام، التحويل البنكي، والبطاقات الائتمانية.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل يمكنني استرداد المبلغ؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، يمكنك استرداد المبلغ خلال 14 يوم من الشراء إذا لم تكن راضية عن المنتج.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">احصلي على منتجك الآن!</h2>
            <p class="order-form-subtitle">املأي النموذج وسنتصل بك في أقرب وقت</p>

            @if($page->product)
            <div class="order-pricing">
                <div class="price-info">
                    <span class="old-price">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                    <span class="current-price">EGP {{ number_format($finalPrice, 2) }}</span>
                </div>
                <div class="shipping-info">
                    <i class="fa-solid fa-truck"></i>
                    <span>شحن مجاني لجميع المحافظات</span>
                </div>
            </div>
            @endif

            <div class="order-form-card" id="order-form">
                @include('landing-templates.partials.order-form')
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">

            <div class="footer-bottom">
                <p>Powered by sawa © {{ date('Y') }}</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/landing-lingerie.js') }}"></script>
</body>

</html>