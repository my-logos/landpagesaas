@php
$templateCss = 'css/landing-games.css';
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
        {{ $page->additional_description ?? 'عرض حصري على الألعاب! احصل على خصم فوري، العرض محدود!' }}
    </div>

    <!-- Hero Section - Centered Colorful Design -->
    <section class="hero-section hero-centered-colorful">
        <div class="hero-gradient-bg"></div>
        <div class="container">
            <div class="hero-content-center">
                <div class="hero-badge-colorful">
                    <i class="fa-solid fa-gamepad"></i>
                    <span>ألعاب حصرية</span>
                </div>
                <h1 class="hero-title-colorful">{{ $page->title ?? ($page->product->name ?? 'ألعاب رائعة') }}</h1>
                <p class="hero-description-colorful">
                    {{ $page->content ?? ($page->product->description ?? 'استمتع بأفضل تجربة ألعاب مع مجموعتنا المميزة من الألعاب. جرافيكس عالية وأداء سلس.') }}
                </p>
                @if($page->product)
                <div class="pricing-colorful">
                    <span class="old-price-colorful">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                    <span class="current-price-colorful">EGP {{ number_format($finalPrice, 2) }}</span>
                    <span class="discount-badge-colorful">خصم {{ round((($page->product->price_cents - ($page->product->price_cents * 0.70)) / $page->product->price_cents) * 100) }}%</span>
                </div>
                @endif
                <div class="countdown" data-hours="24">
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
                <div class="hero-image-center">
                    @if($page->product && $page->product->images)
                    @if($mainImage)
                    <img src="{{ $mainImage }}" alt="{{ $page->product->name ?? 'Product' }}" class="hero-image-rounded">
                    @else
                    <div class="hero-placeholder-colorful">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    @endif
                    @else
                    <div class="hero-placeholder-colorful">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    @endif
                </div>
                <a href="#order-form" class="btn-colorful-cta">
                    <i class="fa-solid fa-download"></i>
                    احصل على لعبتك الآن
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">مميزات الألعاب</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-tv"></i>
                    </div>
                    <h3 class="feature-title">جرافيكس متطورة</h3>
                    <p class="feature-description">جرافيكس عالية الجودة تجعلك تعيش التجربة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="feature-title">لعب جماعي</h3>
                    <p class="feature-description">استمتع باللعب مع أصدقائك عبر الإنترنت</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h3 class="feature-title">إنجازات ومكافآت</h3>
                    <p class="feature-description">احصل على إنجازات ومكافآت حصرية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>
                    <h3 class="feature-title">متعدد المنصات</h3>
                    <p class="feature-description">متاح على جميع الأجهزة والمنصات</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Gallery Section -->
    @if($page->product && $page->product->images)
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">معرض الألعاب</h2>
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
                    <h2 class="details-title">مميزات الألعاب</h2>
                    <ul class="details-list">
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-palette"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>جرافيكس مذهلة</strong>
                                <span>جرافيكس عالية الجودة تجعلك تعيش التجربة الحقيقية</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-wifi"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>لعب أونلاين</strong>
                                <span>استمتع باللعب مع لاعبين من جميع أنحاء العالم</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>دعم فني 24/7</strong>
                                <span>فريق دعم فني متاح على مدار الساعة لمساعدتك</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-sync"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>تحديثات مستمرة</strong>
                                <span>تحديثات دورية تضيف محتوى وميزات جديدة</span>
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
                    <h3 class="why-choose-title">مجموعة ضخمة</h3>
                    <p class="why-choose-description">آلاف الألعاب من جميع الأنواع والفئات</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">02</div>
                    <h3 class="why-choose-title">أسعار مناسبة</h3>
                    <p class="why-choose-description">أفضل الأسعار مع عروض وخصومات مستمرة</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">03</div>
                    <h3 class="why-choose-title">تحميل فوري</h3>
                    <p class="why-choose-description">احصل على الألعاب فوراً بعد الشراء</p>
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
            <h2 class="section-title">آراء اللاعبين</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-purple">ع</div>
                        <div class="testimonial-info">
                            <h4>عمر أحمد</h4>
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
                    <p class="testimonial-text">"أفضل موقع ألعاب تعاملت معه. الجودة عالية والأسعار معقولة. التحميل كان سريع جداً."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-pink">م</div>
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
                    <p class="testimonial-text">"خدمة رائعة ومجموعة ألعاب ضخمة. الجرافيكس مذهلة والأداء سلس. أنصح الجميع."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-cyan">ي</div>
                        <div class="testimonial-info">
                            <h4>يوسف محمود</h4>
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
                    <p class="testimonial-text">"أفضل تجربة ألعاب. الدعم الفني ممتاز والتحديثات مستمرة. سأستمر في الشراء منهم."</p>
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
                        <span>كيف يمكنني تحميل الألعاب؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>بعد الشراء، ستحصل على رابط تحميل فوري عبر البريد الإلكتروني. يمكنك تحميل اللعبة مباشرة.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل الألعاب أصلية؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، جميع الألعاب أصلية ومرخصة مع ضمان الجودة الكامل.</p>
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
                        <p>نعم، يمكنك استرداد المبلغ خلال 7 أيام من الشراء إذا لم تكن راضياً عن المنتج.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">احصل على لعبتك الآن!</h2>
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

    <script src="{{ asset('js/landing-games.js') }}"></script>
</body>

</html>