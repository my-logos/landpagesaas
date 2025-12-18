@php
$templateCss = 'landpage/css/landing-watches.css';
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

    <!-- Hero Section - Classic Refined Design -->
    <section class="hero-section hero-classic-refined">
        <div class="container">
            <div class="hero-refined-content">
                <div class="hero-refined-text">
                    <div class="hero-refined-label">
                        <span class="label-line"></span>
                        <span class="label-text">ساعات فاخرة</span>
                        <span class="label-line"></span>
                    </div>
                    <h1 class="hero-title-refined">{{ $page->title ?? ($page->product->name ?? 'ساعات أنيقة') }}</h1>
                    @php
                    $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
                    $heroDescription = $aiContent['description'] ?? ($page->product->description ?? '');
                    @endphp
                    <p class="hero-description-refined">
                        {{ $heroDescription }}
                    </p>
                    @if($page->product)
                    <div class="pricing-refined">
                        <span class="current-price-refined">EGP {{ number_format($finalPrice, 2) }}</span>
                        <span class="old-price-refined">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                        <span class="discount-refined">خصم {{ round((($page->product->price_cents - ($page->product->price_cents * 0.75)) / $page->product->price_cents) * 100) }}%</span>
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
                    <a href="#order-form" class="btn-refined-cta">
                        <span>اكتشف المجموعة</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <div class="hero-refined-visual">
                    @if($page->product && $page->product->images)
                    @if($mainImage)
                    <div class="hero-refined-image-wrapper">
                        <div class="refined-border"></div>
                        <img src="{{ $mainImage }}" alt="{{ $page->product->name ?? 'Product' }}" class="hero-refined-image">
                    </div>
                    @else
                    <div class="hero-refined-placeholder">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    @endif
                    @else
                    <div class="hero-refined-placeholder">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    @endif
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

    <!-- Product Gallery Section -->
    @if($page->product && $page->product->images)
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
                    @if($page->product && $page->product->images)
                    @if($detailImage)
                    <img src="{{ $detailImage }}" alt="Product Details">
                    @endif
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

    <script src="{{ asset('js/landing-faq-toggle.js') }}"></script>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">

            <div class="footer-bottom">
                <p>Powered by {{ $settings['site_name'] ?? 'DropSaas' }} © {{ date('Y') }}</p>
            </div>
        </div>
    </footer>
</body>

</html>