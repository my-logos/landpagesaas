@php
$templateCss = 'css/landing-decoration-furniture.css';
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
        {{ $page->additional_description ?? 'عرض حصري على أنظمة الديكورات والأثاث! احجز الآن واحصل على خصم فوري، العرض محدود!' }}
    </div>

    <!-- Hero Section - Full Width with Overlay -->
    <section class="hero-section hero-fullwidth">
        @if($page->product && $page->product->images)
        @if($mainImage)
        <div class="hero-background-image" style="background-image: url('{{ $mainImage }}');"></div>
        @endif
        @endif
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content-centered">
                <div class="hero-badge">
                    <i class="fa-solid fa-star"></i>
                    <span>تصميمات احترافية</span>
                </div>
                <h1 class="hero-title-large">{{ $page->title ?? ($page->product->name ?? 'أنظمة الديكورات والأثاث') }}</h1>
                <p class="hero-description-large">
                    {{ $page->content ?? ($page->product->description ?? 'حول منزلك إلى تحفة فنية مع أنظمة الديكورات والأثاث المبتكرة. جودة عالية وتصميمات عصرية تناسب جميع الأذواق.') }}
                </p>
                @if($page->product)
                <div class="pricing-large">
                    <span class="old-price-large">EGP {{ number_format($smartCouponsEnabled ? $increasedPrice : $originalPrice, 2) }}</span>
                    <span class="current-price-large">EGP {{ number_format($finalPrice, 2) }}</span>
                    <span class="discount-badge-large">خصم {{ $discountPercent }}%</span>
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
                <a href="#order-form" class="btn-hero-cta">
                    <i class="fa-solid fa-phone"></i>
                    احجز استشارة مجانية
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">لماذا نحن الأفضل في أنظمة الديكورات والأثاث؟</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3 class="feature-title">تصميمات مبتكرة</h3>
                    <p class="feature-description">فريق من المصممين المحترفين يقدمون لك أحدث صيحات الديكور</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-hammer"></i>
                    </div>
                    <h3 class="feature-title">تركيب احترافي</h3>
                    <p class="feature-description">فريق متخصص في التركيب والتشطيب بمعايير عالمية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <h3 class="feature-title">ضمان شامل</h3>
                    <p class="feature-description">ضمان على جميع المنتجات والخدمات لمدة عام كامل</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3 class="feature-title">تسليم سريع</h3>
                    <p class="feature-description">تسليم في الوقت المحدد مع متابعة مستمرة</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Gallery Section -->
    @if($page->product && $page->product->images)
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">معرض أعمالنا</h2>
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

    <!-- Product Details Section - Vertical Cards Layout -->
    <section class="product-details-section">
        <div class="container">
            <h2 class="section-title">مميزات أنظمتنا</h2>
            <div class="details-cards-grid">
                <div class="detail-card">
                    <div class="detail-card-icon">
                        <i class="fa-solid fa-ruler-combined"></i>
                    </div>
                    <h3 class="detail-card-title">قياسات دقيقة</h3>
                    <p class="detail-card-text">قياسات احترافية تضمن التطابق الكامل مع مساحتك</p>
                </div>
                <div class="detail-card">
                    <div class="detail-card-icon">
                        <i class="fa-solid fa-paint-roller"></i>
                    </div>
                    <h3 class="detail-card-title">خامات عالية الجودة</h3>
                    <p class="detail-card-text">نستخدم أفضل الخامات المستوردة والمحلية المعتمدة</p>
                </div>
                <div class="detail-card">
                    <div class="detail-card-icon">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h3 class="detail-card-title">حلول إضاءة متطورة</h3>
                    <p class="detail-card-text">أنظمة إضاءة ذكية تضيف لمسة سحرية لمساحتك</p>
                </div>
                <div class="detail-card">
                    <div class="detail-card-icon">
                        <i class="fa-solid fa-home"></i>
                    </div>
                    <h3 class="detail-card-title">تصميمات شاملة</h3>
                    <p class="detail-card-text">تصميم كامل للمنزل من الألف إلى الياء</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">سنوات خبرة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">مشروع منجز</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">رضا العملاء</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">دعم فني</div>
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
                        <div class="testimonial-avatar testimonial-avatar-gold">م</div>
                        <div class="testimonial-info">
                            <h4>محمد علي</h4>
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
                    <p class="testimonial-text">"خدمة رائعة وتصميمات احترافية. فريق العمل كان محترف جداً والنتيجة تجاوزت توقعاتي. أنصح الجميع بالتعامل معهم."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-blue">س</div>
                        <div class="testimonial-info">
                            <h4>سارة أحمد</h4>
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
                    <p class="testimonial-text">"اشتريت نظام ديكور كامل للمنزل وكانت النتيجة مذهلة. الجودة عالية والأسعار معقولة. شكراً لكم على المجهود الرائع."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar testimonial-avatar-green">أ</div>
                        <div class="testimonial-info">
                            <h4>أحمد محمود</h4>
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
                    <p class="testimonial-text">"أفضل شركة ديكورات تعاملت معها. التصميم كان عصري والتنفيذ كان دقيق. الضمان شامل والخدمة ممتازة."</p>
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
                        <span>ما هي مدة التنفيذ لمشروع الديكورات؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>مدة التنفيذ تختلف حسب حجم المشروع. المشاريع الصغيرة تستغرق من أسبوع إلى أسبوعين، بينما المشاريع الكبيرة قد تستغرق من شهر إلى شهرين.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل تقدمون خدمة التصميم المجاني؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، نقدم خدمة التصميم المجاني عند التعاقد على المشروع. كما نوفر استشارات مجانية لتحديد احتياجاتك.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>ما هي طرق الدفع المتاحة؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نقبل الدفع نقداً عند الاستلام، التحويل البنكي، والدفع بالتقسيط على عدة دفعات حسب المشروع.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>هل تقدمون ضمان على الأعمال؟</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، نقدم ضمان شامل لمدة عام كامل على جميع الأعمال والمنتجات. كما نوفر خدمة الصيانة الدورية.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Form Section -->
    <section class="order-form-section">
        <div class="container">
            <h2 class="order-form-title">احجز استشارة مجانية الآن!</h2>
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

    <script src="{{ asset('js/landing-decoration-furniture.js') }}"></script>
</body>

</html>