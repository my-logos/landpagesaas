@php
$templateCss = 'css/landing-digital-products.css';
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
        {{ $page->additional_description ?? 'عرض حصري على المنتجات الرقمية! احصل على خصم فوري، العرض محدود!' }}
    </div>

    <!-- Hero Section - Minimal Clean Design -->
    <section class="hero-section hero-minimal-clean">
        <div class="container">
            <div class="hero-minimal-content">
                <div class="hero-minimal-text">
                    <div class="hero-badge-minimal">
                        <i class="fa-solid fa-microchip"></i>
                        <span>منتجات رقمية</span>
                    </div>
                    <h1 class="hero-title-minimal">{{ $page->title ?? ($page->product->name ?? 'منتجات رقمية متطورة') }}</h1>
                    <p class="hero-description-minimal">
                        {{ $page->content ?? ($page->product->description ?? 'اكتشف مجموعة واسعة من المنتجات الرقمية عالية الجودة. حلول مبتكرة لتسريع نمو عملك.') }}
                    </p>
                    @if($page->product)
                    <div class="pricing-minimal">
                        <span class="current-price-minimal">EGP {{ number_format($finalPrice, 2) }}</span>
                        <span class="old-price-minimal">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                        <span class="discount-minimal">خصم {{ round((($page->product->price_cents - ($page->product->price_cents * 0.85)) / $page->product->price_cents) * 100) }}%</span>
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
                    <a href="#order-form" class="btn-minimal-cta">
                        <i class="fa-solid fa-download"></i>
                        احصل على منتجك الآن
                    </a>
                </div>
                <div class="hero-minimal-visual">
                    @if($page->product && $page->product->images)
                    @if($mainImage)
                    <div class="hero-image-container-minimal">
                        <img src="{{ $mainImage }}" alt="{{ $page->product->name ?? 'Product' }}" class="hero-image-minimal">
                        <div class="hero-image-glow"></div>
                    </div>
                    @else
                    <div class="hero-placeholder-minimal">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    @endif
                    @else
                    <div class="hero-placeholder-minimal">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">مميزات منتجاتنا الرقمية</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="feature-title">أداء عالي</h3>
                    <p class="feature-description">منتجات محسّنة للأداء العالي والسرعة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">آمن ومحمي</h3>
                    <p class="feature-description">حماية كاملة لبياناتك ومعلوماتك</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-sync"></i>
                    </div>
                    <h3 class="feature-title">تحديثات مستمرة</h3>
                    <p class="feature-description">تحديثات مجانية وميزات جديدة باستمرار</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h3 class="feature-title">دعم فني 24/7</h3>
                    <p class="feature-description">فريق دعم فني متاح على مدار الساعة</p>
                </div>
            </div>
        </div>
    </section>

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
                                <i class="fa-solid fa-code"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>كود نظيف ومحسّن</strong>
                                <span>كود نظيف ومحسّن يضمن الأداء العالي</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>متجاوب بالكامل</strong>
                                <span>يعمل بشكل مثالي على جميع الأجهزة</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>متعدد اللغات</strong>
                                <span>يدعم عدة لغات بما فيها العربية</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-file-code"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>وثائق شاملة</strong>
                                <span>وثائق مفصلة لمساعدتك في الاستخدام</span>
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
            <h2 class="section-title">لماذا تختارنا؟</h2>
            <div class="why-choose-grid">
                <div class="why-choose-card">
                    <div class="why-choose-number">01</div>
                    <h3 class="why-choose-title">جودة عالية</h3>
                    <p class="why-choose-description">منتجات عالية الجودة تم اختبارها بدقة</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">02</div>
                    <h3 class="why-choose-title">أسعار مناسبة</h3>
                    <p class="why-choose-description">أفضل الأسعار مع عروض وخصومات مستمرة</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">03</div>
                    <h3 class="why-choose-title">دعم فني ممتاز</h3>
                    <p class="why-choose-description">فريق دعم فني محترف متاح دائماً</p>
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
                        <div class="testimonial-avatar testimonial-avatar-blue">أ</div>
                        <div class="testimonial-info">
                            <h4>أحمد خالد</h4>
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
                    <p class="testimonial-text">"منتجات رقمية ممتازة وسهلة الاستخدام. الجودة عالية والدعم الفني سريع. أنصح الجميع."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-teal">س</div>
                        <div class="testimonial-info">
                            <h4>سارة محمد</h4>
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
                    <p class="testimonial-text">"اشتريت عدة منتجات رقمية وكانت جميعها ممتازة. التحديثات مستمرة والدعم الفني متاح دائماً."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-indigo">م</div>
                        <div class="testimonial-info">
                            <h4>محمد علي</h4>
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
                    <p class="testimonial-text">"أفضل موقع للمنتجات الرقمية. الجودة عالية والأسعار معقولة. سأستمر في الشراء منهم."</p>
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
                        <span>كيف يمكنني تحميل المنتج بعد الشراء؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>بعد الشراء، ستحصل على رابط تحميل فوري عبر البريد الإلكتروني. يمكنك تحميل المنتج مباشرة.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل المنتجات أصلية ومرخصة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، جميع المنتجات أصلية ومرخصة مع ضمان الجودة الكامل.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل أحصل على تحديثات مجانية؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، جميع التحديثات مجانية مدى الحياة بعد الشراء.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل يمكنني استرداد المبلغ؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، يمكنك استرداد المبلغ خلال 30 يوم من الشراء إذا لم تكن راضياً عن المنتج.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">احصل على منتجك الرقمي الآن!</h2>
            <p class="order-form-subtitle">املأ النموذج واحصل على رابط التحميل فوراً</p>

            @if($page->product)
            <div class="order-pricing">
                <div class="price-info">
                    <span class="old-price">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                    <span class="current-price">EGP {{ number_format($finalPrice, 2) }}</span>
                </div>
                <div class="shipping-info">
                    <i class="fa-solid fa-download"></i>
                    <span>تحميل فوري بعد الشراء</span>
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

    <script src="{{ asset('js/landing-digital-products.js') }}"></script>
</body>

</html>