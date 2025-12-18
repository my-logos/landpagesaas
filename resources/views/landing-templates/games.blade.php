@php
$templateCss = 'landpage/css/landing-games.css';
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
                @php
                $aiContent = is_string($page->content) ? json_decode($page->content, true) : ($page->content ?? []);
                $heroDescription = $aiContent['description'] ?? ($page->product->description ?? '');
                @endphp
                <p class="hero-description-colorful">
                    {{ $heroDescription }}
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
                <p>Powered by {{ $settings['site_name'] ?? 'DropSaas' }} © {{ date('Y') }}</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/landing-faq-toggle.js') }}"></script>
</body>

</html>