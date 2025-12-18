@php
$templateCss = 'landpage/css/landing-smart-watch.css';
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
        {{ $page->additional_description ?? '' }}
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="product-card">
                    <div class="discount-badge">خصم {{ $discountPercent }}%</div>
                    <div class="product-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="product-brand">ECOMMERCE</div>
                </div>

                <div>
                    <h1 class="product-title">{{ $page->title ?? ($page->product->name ?? 'المنتج') }}</h1>
                    @php
                    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
                    $heroDescription = $aiContent['description'] ?? ($page->product->description ?? '');
                    @endphp
                    <p class="product-description">
                        {{ $heroDescription }}
                    </p>

                    @if($page->product)
                    <div class="pricing">
                        <span class="old-price">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                        <span class="current-price">EGP {{ number_format($finalPrice, 2) }}</span>
                        <div class="shipping-info">
                            رسوم الشحن: {{ number_format($shippingPrice, 2) }} EGP | الدفع عند الاستلام متاح
                        </div>
                    </div>
                    @endif

                    <div class="countdown" data-hours="48">
                        <p class="countdown-text">لا تفوت الفرصة العرض ينتهي قريباً ....</p>
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
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    @php
    if (!isset($aiContent)) {
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    }
    $features = $aiContent['features'] ?? [];
    @endphp
    @if(!empty($features))
    @php
    // Limit features to maximum 6 items (3 per row, max 2 rows)
    $limitedFeatures = array_slice($features, 0, 6);
    @endphp
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">{{ ($aiContent['language'] ?? 'ar') === 'ar' ? 'مميزات المنتج' : 'Product Features' }}</h2>
            <div class="features-grid">
                @foreach($limitedFeatures as $feature)
                <div class="feature-card">
                    @if(isset($feature['icon']))
                    <div class="feature-icon">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    @endif
                    <h3 class="feature-title">{{ $feature['title'] ?? '' }}</h3>
                    <p class="feature-description">{{ $feature['description'] ?? '' }}</p>
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
                <div class="product-card">
                    <div class="product-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="product-brand">Easy ECOMMERCE</div>
                </div>

                <div>
                    <h2 class="details-title">إمكانيات رقمية متطورة</h2>
                    <ul class="details-list">
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="details-text">
                                <strong>تتبع الوقت بدقة</strong>
                                <span>ساعة ذكية دقيقة مع ميزات متقدمة لتتبع الوقت والأنشطة</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-person-walking"></i>
                            </div>
                            <div class="details-text">
                                <strong>تتبع اللياقة البدنية</strong>
                                <span>راقب خطواتك ومسافاتك وسعراتك الحرارية المحروقة</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-battery-full"></i>
                            </div>
                            <div class="details-text">
                                <strong>بطارية طويلة الأمد</strong>
                                <span>بطارية تدوم لأيام طويلة مع شحن سريع</span>
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
            <h2 class="section-title">لماذا يختارها المصريون؟</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h3 class="feature-title">قيمة لا تقارن</h3>
                    <p class="feature-description">أفضل سعر في السوق مع جودة عالية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="feature-title">عصرية وعملية</h3>
                    <p class="feature-description">تصميم عصري يناسب جميع الأذواق</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-sun"></i>
                    </div>
                    <h3 class="feature-title">مثالية للطقس المصري</h3>
                    <p class="feature-description">مقاومة للماء والغبار والعوامل الجوية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="feature-title">دعم فني</h3>
                    <p class="feature-description">فريق دعم محلي متاح دائماً</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title">آراء عملائنا في مصر</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-green">ي</div>
                        <div class="testimonial-info">
                            <h4>يوسف سعيد</h4>
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
                    <p class="testimonial-text">"منتج رائع جداً، جودة عالية وسعر مناسب. التوصيل كان سريع جداً والتغليف ممتاز."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-blue">ف</div>
                        <div class="testimonial-info">
                            <h4>فاطمة أحمد</h4>
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
                    <p class="testimonial-text">"اشتريت المنتج منذ شهر وأنا سعيدة جداً. المميزات كثيرة والبطارية تدوم طويلاً."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-purple">أ</div>
                        <div class="testimonial-info">
                            <h4>أحمد محمود</h4>
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
                    <p class="testimonial-text">"خدمة عملاء ممتازة وتوصيل سريع. المنتج أفضل مما توقعت والضمان شامل."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    @php
    if (!isset($aiContent)) {
    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
    }
    $faqs = $aiContent['faqs'] ?? [];
    @endphp
    @if(!empty($faqs))
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">{{ ($aiContent['language'] ?? 'ar') === 'ar' ? 'أسئلة متكررة' : 'Frequently Asked Questions' }}</h2>
            <div class="faq-list">
                @foreach($faqs as $faq)
                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ $faq['question'] ?? '' }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>{{ $faq['answer'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">أكمل طلبك الآن واستمتع بالخصم!</h2>

            @if($page->product)
            <div class="order-pricing">
                <span class="old-price">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                <span class="current-price">EGP {{ number_format($finalPrice, 2) }}</span>
                <div class="shipping-info">شحن {{ number_format($shippingPrice, 2) }} EGP لجميع المحافظات</div>
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
            <p>Powered by {{ $settings['site_name'] ?? 'DropSaas' }} © {{ date('Y') }}</p>
        </div>
    </footer>

    <script src="{{ asset('js/landing-faq-toggle.js') }}"></script>
</body>

</html>