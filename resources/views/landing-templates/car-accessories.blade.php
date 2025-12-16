@php
$templateCss = 'css/landing-car-accessories.css';
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
        {{ $page->additional_description ?? 'عرض حصري على إكسسوارات السيارات! احصل على خصم فوري، العرض محدود!' }}
    </div>

    <!-- Hero Section - Split Dark Design -->
    <section class="hero-section hero-split-dark">
        <div class="hero-left">
            <div class="hero-content-left">
                <div class="hero-badge-dark">
                    <i class="fa-solid fa-car"></i>
                    <span>إكسسوارات أصلية</span>
                </div>
                <h1 class="hero-title-dark">{{ $page->title ?? ($page->product->name ?? 'إكسسوارات السيارات') }}</h1>
                <p class="hero-description-dark">
                    {{ $page->content ?? ($page->product->description ?? 'قم بتجهيز سيارتك بأفضل الإكسسوارات الأصلية. جودة عالية وتصميمات رياضية تناسب جميع أنواع السيارات.') }}
                </p>
                @if($page->product)
                <div class="pricing-dark">
                    <div class="price-row">
                        <span class="old-price-dark">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                        <span class="current-price-dark">EGP {{ number_format($finalPrice, 2) }}</span>
                    </div>
                    <span class="discount-badge-dark">خصم {{ $discountPercent }}%</span>
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
                <a href="#order-form" class="btn-dark-cta">
                    <i class="fa-solid fa-shopping-cart"></i>
                    اطلب الآن
                </a>
            </div>
        </div>
        <div class="hero-right">
            @if($page->product && $page->product->images)
            @if($mainImage)
            <img src="{{ $mainImage }}" alt="{{ $page->product->name ?? 'Product' }}" class="hero-image-full">
            @else
            <div class="hero-placeholder-dark">
                <i class="fa-solid fa-car-side"></i>
            </div>
            @endif
            @else
            <div class="hero-placeholder-dark">
                <i class="fa-solid fa-car-side"></i>
            </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">لماذا تختار إكسسواراتنا؟</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">جودة أصلية</h3>
                    <p class="feature-description">إكسسوارات أصلية معتمدة من الشركات المصنعة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-tools"></i>
                    </div>
                    <h3 class="feature-title">تركيب احترافي</h3>
                    <p class="feature-description">فريق متخصص في التركيب والصيانة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <h3 class="feature-title">ضمان شامل</h3>
                    <p class="feature-description">ضمان على جميع المنتجات لمدة عام كامل</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">تسليم سريع</h3>
                    <p class="feature-description">تسليم في نفس اليوم داخل القاهرة</p>
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
                    <h2 class="details-title">مميزات الإكسسوارات</h2>
                    <ul class="details-list">
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-car-rear"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>متوافق مع جميع الموديلات</strong>
                                <span>إكسسوارات متوافقة مع جميع أنواع وموديلات السيارات</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-snowflake"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>مقاوم للعوامل الجوية</strong>
                                <span>خامات عالية الجودة مقاومة للشمس والمطر</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-gauge-high"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>أداء عالي</strong>
                                <span>تحسين أداء السيارة وكفاءة الوقود</span>
                            </div>
                        </li>
                        <li class="details-item">
                            <div class="details-icon">
                                <i class="fa-solid fa-palette"></i>
                            </div>
                            <div class="details-text-content">
                                <strong>تصميمات عصرية</strong>
                                <span>تصميمات رياضية وعصرية تناسب جميع الأذواق</span>
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
                    <h3 class="why-choose-title">خبرة طويلة</h3>
                    <p class="why-choose-description">أكثر من 10 سنوات في مجال إكسسوارات السيارات</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">02</div>
                    <h3 class="why-choose-title">أسعار تنافسية</h3>
                    <p class="why-choose-description">أفضل الأسعار في السوق مع جودة لا مثيل لها</p>
                </div>
                <div class="why-choose-card">
                    <div class="why-choose-number">03</div>
                    <h3 class="why-choose-title">خدمة عملاء متميزة</h3>
                    <p class="why-choose-description">فريق دعم متاح على مدار الساعة لخدمتك</p>
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
                        <div class="testimonial-avatar testimonial-avatar-red">خ</div>
                        <div class="testimonial-info">
                            <h4>خالد محمود</h4>
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
                    <p class="testimonial-text">"اشتريت عدة إكسسوارات لسيارتي وكانت الجودة ممتازة. التركيب كان احترافي والنتيجة رائعة. أنصح الجميع بالتعامل معهم."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-blue">أ</div>
                        <div class="testimonial-info">
                            <h4>أحمد سعيد</h4>
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
                    <p class="testimonial-text">"خدمة رائعة وتوصيل سريع. المنتجات أصلية والضمان شامل. سأتعامل معهم مرة أخرى بالتأكيد."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-orange">م</div>
                        <div class="testimonial-info">
                            <h4>مصطفى علي</h4>
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
                    <p class="testimonial-text">"أفضل متجر إكسسوارات سيارات. الأسعار معقولة والجودة عالية. فريق العمل محترف جداً."</p>
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
                        <span>هل الإكسسوارات أصلية؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، جميع الإكسسوارات أصلية ومعتمدة من الشركات المصنعة مع شهادات ضمان.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل تقدمون خدمة التركيب؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، نوفر خدمة التركيب الاحترافي في ورشتنا أو في موقعك داخل القاهرة الكبرى.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>ما هي طرق الدفع المتاحة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نقبل الدفع نقداً عند الاستلام، التحويل البنكي، والدفع بالتقسيط على عدة دفعات.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل تقدمون ضمان على المنتجات؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، نقدم ضمان شامل لمدة عام كامل على جميع المنتجات مع خدمة الصيانة الدورية.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">اطلب إكسسواراتك الآن!</h2>
            <p class="order-form-subtitle">املأ النموذج وسنتصل بك في أقرب وقت</p>

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

    <script src="{{ asset('js/landing-car-accessories.js') }}"></script>
</body>

</html>